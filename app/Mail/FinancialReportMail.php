<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class FinancialReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reportData;
    public $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct($reportData, $pdfContent)
    {
        $this->reportData = $reportData;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $frequency = ucfirst($this->reportData['frequency'] ?? 'Weekly');
        return new Envelope(
            subject: "{$frequency} Financial Report",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.financial_report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $frequency = strtolower($this->reportData['frequency'] ?? 'weekly');
        $filename = "{$frequency}_financial_report_" . date('Y_m_d') . ".pdf";
        
        return [
            Attachment::fromData(fn () => $this->pdfContent, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
