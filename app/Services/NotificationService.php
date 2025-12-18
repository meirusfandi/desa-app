class NotificationService
{
    public function __construct(
        protected WhatsAppService $wa
    ) {}

    public function notifyWarga($user, $message)
    {
        $this->wa->send($user->phone, $message);
        $user->notify(new SuratStatusNotification($message));
    }
}
