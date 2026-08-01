<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private const INBOX = 'owner@example.com';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('sortifya.contact.to', self::INBOX);
        config()->set('mail.from.address', 'no-reply@sortifya.com');
    }

    /** @return array<string, string> */
    private function validMessage(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Rania Haddad',
            'email' => 'rania@example.com',
            'subject' => 'Payout has not arrived',
            'message' => 'I requested a payout three days ago and it has not arrived yet.',
        ], $overrides);
    }

    /*
    |--------------------------------------------------------------------------
    | Delivery
    |--------------------------------------------------------------------------
    */

    public function test_a_message_is_stored_and_emailed(): void
    {
        Mail::fake();

        $this->post(route('contact.send'), $this->validMessage())
            ->assertRedirect(route('contact'))
            ->assertSessionHas('success');

        $stored = ContactMessage::sole();
        $this->assertSame('rania@example.com', $stored->email);
        $this->assertSame('new', $stored->status);
        $this->assertTrue($stored->was_emailed);

        Mail::assertSent(ContactMessageMail::class, fn (ContactMessageMail $mail) => $mail->hasTo(self::INBOX));
    }

    /**
     * The header that makes "reply" work.
     *
     * From must stay the platform's own verified sender — a relay will not
     * send mail claiming to be a stranger's address, so putting the visitor
     * there gets the message rejected or spam-filed.
     */
    public function test_the_visitor_is_on_reply_to_and_never_on_from(): void
    {
        Mail::fake();

        $this->post(route('contact.send'), $this->validMessage());

        Mail::assertSent(ContactMessageMail::class, function (ContactMessageMail $mail) {
            // The envelope deliberately sets no From, so Laravel applies the
            // configured (and relay-verified) sender. Overriding it with the
            // visitor's address is the bug this guards against.
            $this->assertNull($mail->envelope()->from, 'The mailable must not override the From address.');
            $this->assertTrue($mail->hasReplyTo('rania@example.com'));

            return true;
        });
    }

    public function test_a_signed_in_worker_is_linked_to_their_message(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('contact.send'), $this->validMessage());

        $this->assertSame($user->id, ContactMessage::sole()->user_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Not losing messages
    |--------------------------------------------------------------------------
    */

    /** Mail is the notification; the row is the record. */
    public function test_a_mail_failure_still_keeps_the_message(): void
    {
        Mail::shouldReceive('to->send')->andThrow(new \RuntimeException('relay down'));

        $this->post(route('contact.send'), $this->validMessage())
            ->assertSessionHas('warning');

        $stored = ContactMessage::sole();
        $this->assertFalse($stored->was_emailed);
        $this->assertSame('Payout has not arrived', $stored->subject);
    }

    public function test_an_unconfigured_inbox_still_keeps_the_message(): void
    {
        Mail::fake();
        config()->set('sortifya.contact.to', null);

        $this->post(route('contact.send'), $this->validMessage())
            ->assertSessionHas('success');

        $this->assertSame(1, ContactMessage::count());
        Mail::assertNothingSent();
    }

    /*
    |--------------------------------------------------------------------------
    | Spam and validation
    |--------------------------------------------------------------------------
    */

    public function test_a_filled_honeypot_is_silently_discarded(): void
    {
        Mail::fake();

        $this->post(route('contact.send'), $this->validMessage([
            'company_website' => 'http://spam.example',
        ]))->assertSessionHas('success'); // Looks identical to success, on purpose.

        $this->assertSame(0, ContactMessage::count());
        Mail::assertNothingSent();
    }

    public function test_incomplete_messages_are_rejected(): void
    {
        Mail::fake();

        $this->post(route('contact.send'), [])
            ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

        // A two-word message is almost always a bot or a mis-click.
        $this->post(route('contact.send'), $this->validMessage(['message' => 'hi']))
            ->assertSessionHasErrors('message');

        $this->post(route('contact.send'), $this->validMessage(['email' => 'not-an-email']))
            ->assertSessionHasErrors('email');

        $this->assertSame(0, ContactMessage::count());
        Mail::assertNothingSent();
    }

    /*
    |--------------------------------------------------------------------------
    | The pages themselves
    |--------------------------------------------------------------------------
    */

    public function test_the_supporting_pages_are_public(): void
    {
        foreach (['contact', 'faq', 'terms', 'privacy'] as $page) {
            $this->get(route($page))->assertOk();
        }
    }

    public function test_legal_pages_interpolate_the_platforms_own_rules(): void
    {
        config()->set('sortifya.company.legal_name', 'Sortifya SAL');
        config()->set('sortifya.minimum_withdrawal', 10.00);

        $this->get(route('terms'))
            ->assertOk()
            ->assertSee('Sortifya SAL')
            ->assertSee('$10.00')
            // A leftover token means a placeholder was never substituted.
            ->assertDontSee(':company')
            ->assertDontSee(':minimum');
    }

    public function test_the_supporting_pages_render_in_arabic(): void
    {
        $this->get(route('locale.switch', 'ar'));

        $this->get(route('terms'))
            ->assertOk()
            ->assertSee('lang="ar" dir="rtl"', false)
            ->assertSee('شروط الخدمة', false);

        $this->get(route('faq'))->assertOk()->assertSee('الأسئلة', false);
    }
}
