<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{SendMail, PendingEmail, Utilisateur};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\{sendNotificationsToPromoteur};
use App\Mail\AllUserMailEmail;

class DeleteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-user';

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
        $email = $this->ask('Email');

        Utilisateur::whereEmail($email)->delete();
    }
}
