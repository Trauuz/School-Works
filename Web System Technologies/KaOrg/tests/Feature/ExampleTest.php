<?php

test('home redirects to membership dashboard', function () {
    $this->get('/')->assertRedirect('/dashboard');
});
