<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesMove', static function (): void {

    if (! entries()->registry()->get('collection.options.actions.delete.enabled')) {
        return;
    }

    $id = entries()->registry()->get('move.id');

    $mediaResourceDirectoryCurrentLocation = PATH['project'] . registry()->get('flextype.settings.media.upload.directory') . '/' . $id;
        
    if (filesystem()->directory($mediaResourceDirectoryCurrentLocation)->exists()) {
        filesystem()->directory($mediaResourceDirectoryCurrentLocation)->delete();
    } 
});