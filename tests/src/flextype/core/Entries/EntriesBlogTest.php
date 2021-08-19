<?php

use Flextype\Entries\Entries;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);

    $settings = filesystem()->file(ROOT_DIR . '/tests/fixtures/settings.yaml')->get();
   
    filesystem()->file(PATH['project'] . '/config/flextype/settings.yaml')->put($settings);
});

afterEach(function () {
   filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('create new blog', function () {
    $blog = filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/blog/blog.yaml')->get();
    expect(entries()->create('blog', serializers()->yaml()->decode($blog)))->toBeTrue();
});

test('create new blog and blog posts', function () {
    $blog = filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/blog/blog.yaml')->get();
    $post1 = filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/blog/post-1/post.md')->get();
    $post2 = filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/blog/post-2/post.md')->get();

    expect(entries()->create('blog', serializers()->yaml()->decode($blog)))->toBeTrue();
    expect(entries()->create('blog/post-1', serializers()->frontmatter()->decode($post1)))->toBeTrue();
    expect(entries()->create('blog/post-2', serializers()->frontmatter()->decode($post2)))->toBeTrue();
});

test('create new blog and blog posts and fetch them', function () {
    $blog = filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/blog/blog.yaml')->get();
    $post1 = filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/blog/post-1/post.md')->get();
    $post2 = filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/blog/post-2/post.md')->get();

    expect(entries()->create('blog', serializers()->yaml()->decode($blog)))->toBeTrue();
    expect(entries()->create('blog/post-1', serializers()->frontmatter()->decode($post1)))->toBeTrue();
    expect(entries()->create('blog/post-2', serializers()->frontmatter()->decode($post2)))->toBeTrue();

    expect(entries()->fetch('blog')->count())->toEqual(11);
    expect(entries()->fetch('blog/post-1')->count())->toEqual(12);
    expect(entries()->fetch('blog/post-2')->count())->toEqual(12);
});