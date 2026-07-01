<?php

test('the home route serves the public request form', function () {
    $response = $this->get(route('reportar.create'));

    $response->assertOk();
});
