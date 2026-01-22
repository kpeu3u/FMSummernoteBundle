<?php

declare(strict_types=1);

namespace FM\SummernoteBundle\Composer;

use Composer\Script\Event;
use FM\SummernoteBundle\Installer\SummernoteInstaller;

class SummernoteScriptHandler
{
    public static function install(Event $event)
    {
        $options = self::getOptions($event);
        $installer = new SummernoteInstaller($options);
        $installer->install($options);
    }

    /**
     * @return array
     */
    protected static function getOptions(Event $event)
    {
        $options = $event->getComposer()->getPackage()->getExtra();
        $notifier = function ($type, $data) use ($event) {
            $io = $event->getIO();

            switch ($type) {
                case SummernoteInstaller::NOTIFY_CLEAR:
                    return $io->askConfirmation(\sprintf('The Summernote library is already installed in "%s". Do you want to overwrite it? [Y/n] ', $data));

                case SummernoteInstaller::NOTIFY_CLEAR_SIZE:
                    $io->write(\sprintf('Clearing %d files/directories...', $data));

                    break;

                case SummernoteInstaller::NOTIFY_DOWNLOAD:
                    $io->write(\sprintf('Downloading Summernote from %s', $data));

                    break;

                case SummernoteInstaller::NOTIFY_DOWNLOAD_COMPLETE:
                    $io->write(\sprintf('Summernote ZIP archive downloaded to %s', $data));

                    break;

                case SummernoteInstaller::NOTIFY_EXTRACT:
                    $io->write(\sprintf('Extracting Summernote to %s', $data));

                    break;
            }
        };

        return array_merge([
            'version' => SummernoteInstaller::VERSION_LATEST,
            'path' => 'public/vendor/summernote',
            'notifier' => $notifier,
        ], $options['fm-summernote'] ?? []);
    }
}
