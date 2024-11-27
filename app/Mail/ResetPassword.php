<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $emailTitle;
    protected $title;
    protected $preHeader;
    protected $headerImgFullUrl;
    protected $arrTextLines;
    protected $actionButtonUrl;
    protected $actionButtonText;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $arrParam)
    {
        $this->emailTitle = $arrParam['EMAIL_TITLE'] ?? 'Novo Email';
        $this->title = $arrParam['TITLE'] ?? 'Mensagem';
        $this->preHeader = $arrParam['PRE_HEADER'] ?? '';
        $this->headerImgFullUrl = $arrParam['HEADER_IMG_FULL_URL'] ?? '';
        $this->arrTextLines = $arrParam['ARR_TEXT_LINES'] ?? [];
        $this->actionButtonUrl = $arrParam['ACTION_BUTTON_URL'] ?? '';
        $this->actionButtonText = $arrParam['ACTION_BUTTON_TEXT'] ?? '';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.basic')
                ->subject($this->emailTitle)
                ->with([
                    'TITLE' => $this->title,
                    'PRE_HEADER' => $this->preHeader,
                    'HEADER_IMG_FULL_URL' => $this->headerImgFullUrl,
                    'ARR_TEXT_LINES' => $this->arrTextLines,
                    'ACTION_BUTTON_URL' => $this->actionButtonUrl,
                    'ACTION_BUTTON_TEXT' => $this->actionButtonText,
                ]);
    }
}
