<?php

test('country_flag converts an ISO code to a flag emoji', function () {
    expect(country_flag('fr'))->toBe('🇫🇷');
});

test('country_flag rejects invalid codes', function () {
    expect(country_flag('xyz'))->toBe('');
});
