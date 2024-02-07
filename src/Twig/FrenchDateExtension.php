<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FrenchDateExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('frenchDate', [$this, 'frenchDateFormat']),
        ];
    }

    public function frenchDateFormat($value): string
    {
        if (!$value instanceof \DateTimeInterface) {
            // Si la valeur n'est pas un DateTimeInterface, essayez de la convertir
            try {
                $value = new \DateTime($value);
            } catch (\Exception $e) {
                // Gérez l'erreur si la conversion échoue
                return '';
            }
        }

        setlocale(LC_TIME, 'fr_FR.UTF-8');
        return strftime('%A %d/%m/%Y %H:%M', $value->getTimestamp());
    }
}
?>