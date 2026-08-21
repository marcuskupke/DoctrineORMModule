<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Assets\GraphEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Part of the test assets used to produce a demo of graphs in the Laminas Developer Tools integration
 *
 * @link    http://www.doctrine-project.org/
 */
#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    protected int $id;

    /** @var Collection|UserGroup[] */
    #[ORM\ManyToMany(targetEntity: UserGroup::class, mappedBy: 'users')]
    protected Collection $groups;

    /** @var Collection|Session[] */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'user')]
    protected Collection $sessions;

    #[ORM\OneToOne(targetEntity: Address::class)]
    protected Address $address;

    public function __construct()
    {
        $this->groups   = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }
}
