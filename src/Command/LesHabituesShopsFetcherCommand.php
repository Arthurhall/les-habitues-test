<?php

namespace App\Command;

use App\Entity\Shop;
use App\Manager\LesHabituesApiManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class LesHabituesShopsFetcherCommand extends Command
{
    const TOTAL_PAGE = 5;

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
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, LesHabituesApiManager $manager, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->manager = $manager;
        $this->serializer = $serializer;

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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->progressStart(5);

        $shopCount = 0;
        for ($page = 1; $page <= self::TOTAL_PAGE; $page++) {
            $localisations = $this->manager->getLocalisations($page);

            foreach ($localisations as $localisation) {
                $shop = $this->em->find(Shop::class, $localisation['id']);
                if (!$shop) {
                    $shop = new Shop();
                    $shop->setId($localisation['id']);
                    $this->em->persist($shop);
                }

                $this->serializer->denormalize(
                    $localisation,
                    Shop::class,
                    'json',
                    [AbstractNormalizer::OBJECT_TO_POPULATE => $shop]
                );
                $shopCount++;
            }
            $this->em->flush();
            $this->em->clear();
            $this->io->progressAdvance();
        }
        $this->io->progressFinish();

        $this->io->section('Total');
        $this->io->listing([
            sprintf('Pages : <info>%d</info>', self::TOTAL_PAGE),
            sprintf('Shop count : <info>%d</info>', $shopCount),
        ]);

        return 0;
    }
}
