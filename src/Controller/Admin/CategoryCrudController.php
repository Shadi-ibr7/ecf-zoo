<?php

namespace App\Controller\Admin;

// Remove the duplicate declaration of 'Category'
use App\Entity\Product; // Add this line
use App\Repository\CategoryRepository; // Add this line
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            BooleanField::new('active'),
            DateTimeField::new('updatedAt')->hideOnForm(),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof Category) return;

        $entityInstance->setCreatedAt(new \DateTimeImmutable);

        parent::persistEntity($em, $entityInstance);
    }

    public function deleteEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof Category) return;

        foreach ($entityInstance->getProducts() as $product) {
            $em->remove($product);
        }

        parent::deleteEntity($em, $entityInstance);
    }
}
    /**
     * @ORM\Entity(repositoryClass=CategoryRepository::class)
     */
    class Category
    {
        // ...
    
        /**
         * @ORM\OneToMany(targetEntity=Product::class, mappedBy="category")
         */
        private $products;
    
        /**
         * @ORM\Column(type="datetime_immutable")
         */
        private $createdAt;
    
        public function __construct()
        {
            $this->products = new ArrayCollection();
        }
    
        /**
         * @return Collection|Product[]
         */
        public function getProducts(): Collection
        {
            return $this->products;
        }
    
        public function setCreatedAt(\DateTimeImmutable $createdAt): void
        {
            $this->createdAt = $createdAt;
        }
    }
