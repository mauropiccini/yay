<?php

namespace App\Api\Tests\Controller;

use App\Api\Test\WebTestCase;
use Component\Entity\Activity;

class ActivityControllerTest extends WebTestCase
{
    public function test_Activity_IndexAction(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/activities/');
        $response = $client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertJson($content = $response->getContent());
        $this->assertInternalType('array', $data = json_decode($content, true));
        $this->assertNotEmpty($data);

        $activities = [];
        foreach ($data as $key => $value) {
            $activities[] = $value['name'];

            $this->assertArrayHasKey('name', $value);
            $this->assertArrayHasKey('created_at', $value);
            $this->assertArrayHasKey('data', $value);

            if (Activity::PERSONAL_ACHIEVEMENT_GRANTED == $value['name']) {
                $this->assertArraySubsetHasKey('data', 'player', $value);
                $this->assertArraySubsetHasKey('data', 'achievement', $value);
                $this->assertArraySubsetHasKey('data', 'achieved_at', $value);

                $this->assertArraySubsetHasKey('links', 'self', $value);
                $this->assertArraySubsetHasKey('links', 'player', $value);
                $this->assertArraySubsetHasKey('links', 'achievement', $value);
            }

            if (Activity::PERSONAL_ACTION_GRANTED == $value['name']) {
                $this->assertArraySubsetHasKey('data', 'player', $value);
                $this->assertArraySubsetHasKey('data', 'action', $value);
                $this->assertArraySubsetHasKey('data', 'achieved_at', $value);

                $this->assertArraySubsetHasKey('links', 'self', $value);
                $this->assertArraySubsetHasKey('links', 'player', $value);
                $this->assertArraySubsetHasKey('links', 'action', $value);
            }

            if (Activity::PLAYER_CREATED == $value['name']) {
                $this->assertArraySubsetHasKey('data', 'player', $value);

                $this->assertArraySubsetHasKey('links', 'self', $value);
                $this->assertArraySubsetHasKey('links', 'player', $value);
            }

            if (Activity::LEVEL_CHANGED == $value['name']) {
                $this->assertArraySubsetHasKey('data', 'player', $value);
                $this->assertArraySubsetHasKey('data', 'level', $value);

                $this->assertArraySubsetHasKey('links', 'self', $value);
                $this->assertArraySubsetHasKey('links', 'player', $value);
            }

            if (Activity::SCORE_CHANGED == $value['name']) {
                $this->assertArraySubsetHasKey('data', 'player', $value);
                $this->assertArraySubsetHasKey('data', 'score', $value);

                $this->assertArraySubsetHasKey('links', 'self', $value);
                $this->assertArraySubsetHasKey('links', 'player', $value);
            }
        }

        $this->assertTrue(in_array(Activity::PERSONAL_ACHIEVEMENT_GRANTED, $activities));
        $this->assertTrue(in_array(Activity::PERSONAL_ACTION_GRANTED, $activities));
        $this->assertTrue(in_array(Activity::PLAYER_CREATED, $activities));
        $this->assertTrue(in_array(Activity::LEVEL_CHANGED, $activities));
        $this->assertTrue(in_array(Activity::SCORE_CHANGED, $activities));

        $client->request('GET', '/api/activities/?limit=1');
        $response = $client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertJson($content = $response->getContent());
        $this->assertInternalType('array', $data = json_decode($content, true));
        $this->assertNotEmpty($data);
        $this->assertCount(1, $data);
    }
}
