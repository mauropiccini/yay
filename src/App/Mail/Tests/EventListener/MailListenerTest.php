<?php

namespace App\Mail\Tests\EventListener;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Swift_Message;
use Component\Engine\Events;
use Component\Engine\Event\ObjectEvent;
use Component\Entity\Achievement\PersonalActionInterface;
use Component\Entity\PlayerInterface;
use Component\Engine\Storage\StorageInterface;
use App\Mail\EventListener\MailListener;
use App\Mail\Service\Mailer;
use Component\Entity\Achievement\LevelCollection;

class MailListenerTest extends WebTestCase
{
    public function test_register(): void
    {
        $client = static::createClient();

        $container = $client->getKernel()->getContainer();
        /** @var EventDispatcherInterface */
        $dispatcher = $container->get('event_dispatcher');

        $events = array_keys($dispatcher->getListeners());
        $this->assertContains(EVENTS::GRANT_PERSONAL_ACHIEVEMENT, $events);
        $this->assertContains(EVENTS::GRANT_PERSONAL_ACTION, $events);
        $this->assertContains(EVENTS::CREATE_PLAYER, $events);

        $calls = [];
        foreach ($dispatcher->getListeners() as $event => $listeners) {
            foreach ($listeners as $listener) {
                list($class, $method) = $listener;
                $calls[] = sprintf('%s::%s', get_class($class), $method);
            }
        }

        $this->assertContains(sprintf('%s::%s', MailListener::class, 'onGrantPersonalAction'), $calls);
        $this->assertContains(sprintf('%s::%s', MailListener::class, 'onGrantPersonalAchievement'), $calls);
        $this->assertContains(sprintf('%s::%s', MailListener::class, 'onCreatePlayer'), $calls);
    }

    public function test_on_grant_personal_action(): void
    {
        $faker = \Faker\Factory::create();

        $storage = $this->createMock(StorageInterface::class);

        $mailer = $this->getMockBuilder(Mailer::class)
            ->disableOriginalConstructor()
            ->setMethods(['send', 'compose'])
            ->getMock();

        $mailer->expects($this->once())
            ->method('send')
            ->willReturn(0);

        $mailer->expects($this->once())
            ->method('compose')
            ->willReturn(new Swift_Message());

        $personalAction = $this->createConfiguredMock(PersonalActionInterface::class, [
            'getPlayer' => $this->createConfiguredMock(PlayerInterface::class, [
                'getEmail' => $faker->email,
            ]),
        ]);

        $event = new ObjectEvent($personalAction);
        $listener = new MailListener($storage, $mailer);
        $listener->onGrantPersonalAction($event);
    }

    public function test_on_grant_personal_achievement(): void
    {
        $faker = \Faker\Factory::create();

        $storage = $this->createMock(StorageInterface::class);

        $mailer = $this->getMockBuilder(Mailer::class)
            ->disableOriginalConstructor()
            ->setMethods(['send', 'compose'])
            ->getMock();

        $mailer->expects($this->once())
            ->method('send')
            ->willReturn(0);

        $mailer->expects($this->once())
            ->method('compose')
            ->willReturn(new Swift_Message());

        $personalAchievement = $this->createConfiguredMock(PersonalActionInterface::class, [
            'getPlayer' => $this->createConfiguredMock(PlayerInterface::class, [
                'getEmail' => $faker->email,
            ]),
        ]);

        $event = new ObjectEvent($personalAchievement);
        $listener = new MailListener($storage, $mailer);
        $listener->onGrantPersonalAchievement($event);
    }

    public function test_on_create_player(): void
    {
        $faker = \Faker\Factory::create();

        $storage = $this->createMock(StorageInterface::class);

        $mailer = $this->getMockBuilder(Mailer::class)
            ->disableOriginalConstructor()
            ->setMethods(['send', 'compose'])
            ->getMock();

        $mailer->expects($this->once())
            ->method('send')
            ->willReturn(0);

        $mailer->expects($this->once())
            ->method('compose')
            ->willReturn(new Swift_Message());

        $player = $this->createConfiguredMock(PlayerInterface::class, [
            'getName' => $faker->text,
            'getEmail' => $faker->email,
        ]);

        $event = new ObjectEvent($player);
        $listener = new MailListener($storage, $mailer);
        $listener->onCreatePlayer($event);
    }

    public function test_on_change_level(): void
    {
        $faker = \Faker\Factory::create();

        $storage = $this->createConfiguredMock(StorageInterface::class, [
            'findLevelBy' => new LevelCollection(),
        ]);

        $mailer = $this->getMockBuilder(Mailer::class)
            ->disableOriginalConstructor()
            ->setMethods(['send', 'compose'])
            ->getMock();

        $mailer->expects($this->once())
            ->method('send')
            ->willReturn(0);

        $mailer->expects($this->once())
            ->method('compose')
            ->willReturn(new Swift_Message());

        $player = $this->createConfiguredMock(PlayerInterface::class, [
            'getName' => $faker->text,
            'getEmail' => $faker->email,
        ]);

        $event = new ObjectEvent($player);
        $listener = new MailListener($storage, $mailer);
        $listener->onChangeLevel($event);
    }

    public function test_on_change_score(): void
    {
        $faker = \Faker\Factory::create();

        $storage = $this->createMock(StorageInterface::class);

        $mailer = $this->getMockBuilder(Mailer::class)
            ->disableOriginalConstructor()
            ->setMethods(['send', 'compose'])
            ->getMock();

        $mailer->expects($this->once())
            ->method('send')
            ->willReturn(0);

        $mailer->expects($this->once())
            ->method('compose')
            ->willReturn(new Swift_Message());

        $player = $this->createConfiguredMock(PlayerInterface::class, [
            'getName' => $faker->text,
            'getEmail' => $faker->email,
        ]);

        $event = new ObjectEvent($player);
        $listener = new MailListener($storage, $mailer);
        $listener->onChangeScore($event);
    }
}
