<?php

namespace App\Command;

use App\Manager\LesHabituesApiManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LesHabituesShopsFetcherCommand extends Command
{
    protected static $defaultName = 'app:fetch-shops';

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LesHabituesApiManager
     */
    private $manager;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, LesHabituesApiManager $manager)
    {
        $this->em = $em;
        $this->manager = $manager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Fetch shops from "les habitués".')
            ->setHelp(<<<EOF
Command <info>%command.name%</info> can inserts shops in db.
Example :

  <info>%command.full_name% argument</info>

EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        dump($this->manager->getShops());

        return 0;
    }
}
