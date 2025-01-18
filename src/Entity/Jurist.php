<?php

namespace App\Entity;

use App\Repository\JuristRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JuristRepository::class)]
class Jurist extends User
{

}
