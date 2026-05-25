<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class BaseMail extends Mailable
{
    use Queueable, SerializesModels;

    protected string $emailTitle;
    protected string $title;
    protected string $preHeader;
    protected string $headerImgFull;
    /**
     * @var array<int, string>
     */
    protected array $arrTextLines;
    protected string $actionButtonUrl;
    protected string $actionButtonText;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    /**
     * @param array{
     *     EMAIL_TITLE?: mixed,
     *     TITLE?: mixed,
     *     PRE_HEADER?: mixed,
     *     HEADER_IMG_FULL?: mixed,
     *     ARR_TEXT_LINES?: array<int, mixed>|mixed,
     *     ACTION_BUTTON_URL?: mixed,
     *     ACTION_BUTTON_TEXT?: mixed
     * } $arrParam
     */
    public function __construct(array $arrParam)
    {
        $this->emailTitle = (string) ($arrParam['EMAIL_TITLE'] ?? 'Novo Email');
        $this->title = (string) ($arrParam['TITLE'] ?? 'Mensagem');
        $this->preHeader = (string) ($arrParam['PRE_HEADER'] ?? '');
        $this->headerImgFull = (string) ($arrParam['HEADER_IMG_FULL'] ?? '');
        $this->arrTextLines = is_array($arrParam['ARR_TEXT_LINES'] ?? null)
            ? array_map(static fn ($line) => (string) $line, $arrParam['ARR_TEXT_LINES'])
            : [];
        $this->actionButtonUrl = (string) ($arrParam['ACTION_BUTTON_URL'] ?? '');
        $this->actionButtonText = (string) ($arrParam['ACTION_BUTTON_TEXT'] ?? '');
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
                    'HEADER_IMG_FULL' => $this->headerImgFull,
                    'ARR_TEXT_LINES' => $this->arrTextLines,
                    'ACTION_BUTTON_URL' => $this->actionButtonUrl,
                    'ACTION_BUTTON_TEXT' => $this->actionButtonText,
                ]);
    }
}
