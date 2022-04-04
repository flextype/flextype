<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! entries()->registry()->get('methods.fetch.collection.fields.published_at.enabled')) {
        return;
    }

    if (entries()->registry()->get('methods.fetch.result.published_at') === null) {
        entries()->registry()->set('methods.fetch.result.published_at', (int) filesystem()->file(entries()->getFileLocation(entries()->registry()->get('methods.fetch.params.id')))->lastModified());
    } else {
        entries()->registry()->set('methods.fetch.result.published_at', (int) strtotime((string) entries()->registry()->get('methods.fetch.result.published_at')));
    }
});

emitter()->addListener('onEntriesCreate', static function (): void {

    if (! entries()->registry()->get('methods.create.collection.fields.published_at.enabled')) {
        return;
    }

    if (entries()->registry()->get('methods.create.params.data.published_at') !== null) {
        return;
    }

    entries()->registry()->set('methods.create.params.data.published_at', date(registry()->get('flextype.settings.date_format'), time()));
});