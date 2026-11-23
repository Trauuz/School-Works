<?php

test('task tracker routes are removed', function () {
    $this->get('/tasks')->assertNotFound();
    $this->post('/tasks')->assertNotFound();
});
