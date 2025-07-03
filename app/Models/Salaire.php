<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salaire extends Model
{
  use HasFactory;

  protected $fillable = [
    'personne_id',
    'montant_vire',
    'date_virement',
    'montant_sanction',
    'montant_prime',
    'salaire_base',
  ];

  protected $casts = [
    'date_virement' => 'date'
  ];

  public function personne()
  {
    return $this->belongsTo(Personne::class);
  }

  protected function dateVirement(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        try {
          return $value ? Carbon::parse($value)->format('d/m/Y') : null;
        } catch (\Exception $e) {
          return null;
        }
      },
      set: function ($value) {
        try {
          if (!$value) return null;

          if (strpos($value, '/') !== false) {
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
          }

          return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
          return null;
        }
      }
    );
  }

  public function getMonthWithLeadingZero()
  {
    // Si c'est un objet Carbon ou DateTimeInterface
    if ($this->date_virement instanceof \DateTimeInterface) {
      return $this->date_virement->format('m');
    }
    // Si c'est une string
    if (is_string($this->date_virement)) {
      $dateStr = (string) $this->date_virement;
      // Vérifie si la string est au format Y-m-d (date SQL)
      if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
        return Carbon::createFromFormat('Y-m-d', $dateStr)->format('m');
      }
      // Vérifie si la string est au format d/m/Y
      if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dateStr)) {
        return Carbon::createFromFormat('d/m/Y', $dateStr)->format('m');
      }
      // Sinon, tente un parse classique
      return Carbon::parse($dateStr)->format('m');
    }
    // Si c'est un objet date (type "date" Laravel, ex: Illuminate\Support\Date)
    if (is_object($this->date_virement) && method_exists($this->date_virement, 'format')) {
      return $this->date_virement->format('m');
    }
    return null;
  }
}
