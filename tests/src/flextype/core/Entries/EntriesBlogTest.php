<?php

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

