<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesCreate', static function (): void {

    if (! entries()->registry()->get('collection.options.actions.create.enabled')) {
        return;
    }

    $id   = entries()->registry()->get('create.id');
    $data = entries()->registry()->get('create.data');

    $data['resource'] = $data['resource'] ?? '';

    if (is_array($data['resource'])) {
    
        $media = upload($data['resource'], $id);
       
        if ($media->name) {

            $uploadDirectory = strings(PATH['project']  . '/' . registry()->get('flextype.settings.upload.directory') . '/' . $id)->reduceSlashes()->toString();
            $mediaFile =  $uploadDirectory . '/media.' . filesystem()->file($media->name)->extension();
            filesystem()->file($uploadDirectory . '/' . $media->name)->move($mediaFile);

            $data['resource'] = strings($id . '/media.' . filesystem()->file($media->name)->extension())->reduceSlashes()->toString();
        } else {
            $data['resource'] = '';
        }
    }

    entries()->registry()->set('create.data', array_merge(entries()->registry()->get('create.data'), $data));
});