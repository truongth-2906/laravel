<?php

namespace App\Providers;

use App\Domains\Announcement\Services\AnnouncementService;
use App\Domains\Country\Services\CountryService;
use App\Domains\Message\Services\MessageService;
use App\Domains\Notification\Services\NotificationService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Class ComposerServiceProvider.
 */
class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @param  AnnouncementService  $announcementService
     */
    public function boot(
        AnnouncementService $announcementService,
        NotificationService $notificationService,
        MessageService $messageService,
        CountryService $countryService
    ) {
        View::composer('*', function ($view) {
            $view->with('logged_in_user', auth()->user());
        });

        View::composer(['frontend.index', 'frontend.layouts.app'], function ($view) use ($announcementService) {
            $view->with('announcements', $announcementService->getForFrontend());
        });

        View::composer(['backend.layouts.app'], function ($view) use ($announcementService) {
            $view->with('announcements', $announcementService->getForBackend());
        });

        View::composer(['frontend.layouts.app', 'frontend.notification.index'], function ($view) use ($notificationService) {
            $view->with('numberUnreadNotification', $notificationService->countUnreadNotifications());
        });

        View::composer(['frontend.layouts.app'], function ($view) use ($messageService) {
            $view->with('numberUnreadMessage', $messageService->countUnreadMessages());
        });

        View::composer([
            'frontend.employer.setting',
            'frontend.freelancer.setting',
            'frontend.job.create',
            'frontend.job.edit',
            'backend.freelancer.create',
            'backend.freelancer.edit',
            'backend.employer.create',
            'backend.employer.edit',
            'backend.job.create',
            'backend.job.edit',
            'frontend.auth.register',
        ], function ($view) use ($countryService) {
            $view->with('countries', $countryService->get());
        });
    }
}
