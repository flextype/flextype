<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesMove', static function (): void {

    if (! entries()->registry()->get('collection.options.actions.move.enabled')) {
        return;
    }

    $id    = entries()->registry()->get('move.id');
    $newID = entries()->registry()->get('move.newID');

    $mediaResourceDirectoryCurrentLocation = PATH['project'] . registry()->get('flextype.settings.media.upload.directory') . '/' . $id;
    $mediaResourceDirectoryNewLocation     = PATH['project'] . registry()->get('flextype.settings.media.upload.directory') . '/' . $newID;
   
    if (filesystem()->directory($mediaResourceDirectoryCurrentLocation)->exists()) {
        filesystem()->directory($mediaResourceDirectoryCurrentLocation)->move($mediaResourceDirectoryNewLocation);
    }
});