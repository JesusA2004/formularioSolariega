<?php

test('the home route redirects to the public request form', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect(route('reportar.create'));
});
