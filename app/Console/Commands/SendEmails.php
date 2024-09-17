<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{SendMail, PendingEmail};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\{sendNotificationsToPromoteur};
use App\Mail\AllUserMailEmail;
use App\Mail\SendMail as Email;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $emails = SendMail::where('is_sent', false)->get();

        foreach ($emails as $email) {
            try {
               Mail::to($email->utilisateur)->send(new Email($email->utilisateur, $email->title, unserialize($email->message)));
                $email->update(['is_sent' => true]);
                echo "Send mail =====\n";
            } catch (\Exception $th) {
                echo "send exception email ==== ";
            }
        }

        if($emails->count()) echo $emails->count()." mails envoyes\n";
    }
}
