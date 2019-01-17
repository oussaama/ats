<?php
namespace App\Command;

use App\Entity\Reviews;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;
use App\Entity\Products;


class exportProductsCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:upload:products';
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Upload products via api service.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to upload products   app:upload:products');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'upload products',
            '============',
            '',
        ]);

        //$result = (array) json_decode(file_get_contents('http://internal.ats-digital.com:3066/api/products'),true);
        $result = (array) json_decode(file_get_contents('C:\wamp64\www\ats\src\Command\database.json'),true);
        if($result!= null){
            foreach($result as $r){
                $product = new Products();
                $product->addProduct($r['productName'],$r['basePrice'],$r['category'],$r['brand'],$r['productMaterial'],$r['imageUrl'],$r['delivery'],$r['details']);
                foreach ($r['reviews'] as $review){
                    $reviewObject = new Reviews();
                    $reviewObject->setRating($review['rating']);
                    $reviewObject->setContent($review['content']);
                    $product->addReview($reviewObject);
                }

                $this->em->persist($product);
                $this->em->flush();
            }
            $output->writeln([
                '',
                '============',
                'upload products is success',
            ]);
        }else{
            $output->writeln([
                '============',
                'no products',
            ]);
        }
    }
}