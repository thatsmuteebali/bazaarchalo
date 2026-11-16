<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use App\Models\WebContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_displays_the_submission_form(): void
    {
        foreach ([
            'Contact Banner Title' => 'Contact Bazaar Chalo',
            'Contact Banner Background Image' => '',
            'Contact Title' => 'Get in touch',
            'Website Address' => '123 Market Street',
            'Website Email' => 'contact@example.com',
            'Website Phone Number' => '555-0100',
            'Website Favicon' => '',
            'Website Facebook Url' => '',
            'Website Instagram Url' => '',
            'Website Tiktok Url' => '',
            'Website Header Logo' => '',
            'Website Footer Logo' => '',
        ] as $field => $value) {
            WebContent::create([
                'page' => 'setting',
                'field' => $field,
                'value' => $value,
                'type' => 'text',
            ]);
        }

        $this->get(route('frontend.contact'))
            ->assertOk()
            ->assertSee('action="' . route('frontend.contact.send') . '"', false)
            ->assertSee('name="name"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="message"', false);
    }

    public function test_contact_form_sends_message_to_configured_website_email(): void
    {
        Mail::fake();
        WebContent::create([
            'page' => 'setting',
            'field' => 'Website Email',
            'value' => 'contact@example.com',
            'type' => 'email',
        ]);

        $this->post(route('frontend.contact.send'), [
            'name' => 'Jordan Smith',
            'email' => 'jordan@example.com',
            'message' => 'I have a question about an order.',
        ])->assertRedirect(route('frontend.contact'))
            ->assertSessionHas('status');

        Mail::assertSent(ContactMessageMail::class, function (ContactMessageMail $mail): bool {
            return $mail->hasTo('contact@example.com')
                && $mail->name === 'Jordan Smith'
                && $mail->email === 'jordan@example.com'
                && $mail->message === 'I have a question about an order.'
                && str_contains($mail->render(), 'New contact inquiry');
        });
    }

    public function test_contact_form_rejects_invalid_input_without_sending_email(): void
    {
        Mail::fake();

        $this->from(route('frontend.contact'))
            ->post(route('frontend.contact.send'), [
                'name' => '',
                'email' => 'not-an-email',
                'message' => '',
            ])
            ->assertRedirect(route('frontend.contact'))
            ->assertSessionHasErrors(['name', 'email', 'message']);

        Mail::assertNothingSent();
    }

    public function test_contact_form_reports_missing_recipient_setting(): void
    {
        Mail::fake();

        $this->from(route('frontend.contact'))
            ->post(route('frontend.contact.send'), [
                'name' => 'Jordan Smith',
                'email' => 'jordan@example.com',
                'message' => 'Please contact me.',
            ])
            ->assertRedirect(route('frontend.contact'))
            ->assertSessionHasErrors('contact');

        Mail::assertNothingSent();
    }
}
