<?php

namespace Visualbuilder\EmailTemplates\Traits;

use Illuminate\Support\Facades\App;
use Visualbuilder\EmailTemplates\Facades\TokenHelper;
use Visualbuilder\EmailTemplates\Models\EmailTemplate;

trait BuildGenericEmail
{

    public $emailTemplate;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->emailTemplate = EmailTemplate::findEmailByKey($this->template, App::currentLocale());

        if ($this->attachment ?? false) {
            $this->attach(
                $this->attachment->getPath(),
                [
                    'as' => $this->attachment->filename,
                    'mime' => $this->attachment->mime_type,
                ]
            );
        }

        $data = EmailTemplate::getEmailData($this->emailTemplate, $this);

        return $this->from($this->emailTemplate->from['email'], $this->emailTemplate->from['name'])
            ->view($this->emailTemplate->view_path)
            ->subject(TokenHelper::replace($this->emailTemplate->subject, $this))
            ->to($this->sendTo)
            ->with(['data' => $data]);
    }
}
