<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class Projects extends Model
{
    protected $fillable = [
        'no_projek',
        'anggota_id',
        'client_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'price',
        'pajak_rate',
        'kas_rate',
        'pajak',
        'kas',
        'komisi',
        'invoice_status',
        'is_done',
    ];

    protected $table = 'projects';

    public function client(): BelongsTo
    {
        return $this->belongsTo(Clients::class, 'client_id');
    }

    public function penanggungjawab(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'anggota_id');
    }

    public function detailProject(): HasMany
    {
        return $this->hasMany(Detail_Project::class);
    }

    protected static function booted()
    {
        static::created(function ($project) {
            $originalData = $project->getOriginal();
            $updatedData = $project->getAttributes();

            if ($project->invoice_status === 'Paid') {

                //data di tabel transaction
                $idCategorieKas = 2;
                $idCategoriePajak = 3;
                $noProjek = $project->no_projek;
                $nameTransaction = $project->name;
                $descriptionTransaction = 'nomor project = ' . $project->no_projek;
                $kas = $project->kas;
                $pajak = $project->pajak;
                $date = now();

                //insert data kas ke tabel transaction
                Transactions::updateOrCreate(
                    ['no_projek' => $noProjek, 'categorie_finance_id' => $idCategorieKas],
                    [
                        'categorie_finance_id' => $idCategorieKas,
                        'no_projek' => $noProjek,
                        'name' => $nameTransaction,
                        'description' => $descriptionTransaction,
                        'total' => $kas,
                        'date_transaction' => $date,
                    ]
                );

                //insert data pajak ke tabel transaction
                Transactions::updateOrCreate(
                    ['no_projek' => $noProjek, 'categorie_finance_id' => $idCategoriePajak],
                    [
                        'categorie_finance_id' => $idCategoriePajak,
                        'no_projek' => $noProjek,
                        'name' => $nameTransaction,
                        'description' => $descriptionTransaction,
                        'total' => $pajak,
                        'date_transaction' => $date,
                    ]
                );
            }
        });

        static::updated(function ($project) {
            $originalData = $project->getOriginal();
            $updatedData = $project->getAttributes();

            if ($project->invoice_status === 'Paid') {

                //data di tabel transaction
                $idCategorieKas = 2;
                $idCategoriePajak = 3;
                $noProjek = $project->no_projek;
                $nameTransaction = $project->name;
                $descriptionTransaction = 'nomor project = ' . $project->no_projek;
                $kas = $project->kas;
                $pajak = $project->pajak;
                $date = now();

                //insert data kas ke tabel transaction
                Transactions::updateOrCreate(
                    ['no_projek' => $noProjek, 'categorie_finance_id' => $idCategorieKas],
                    [
                        'categorie_finance_id' => $idCategorieKas,
                        'no_projek' => $noProjek,
                        'name' => $nameTransaction,
                        'description' => $descriptionTransaction,
                        'total' => $kas,
                        'date_transaction' => $date,
                    ]
                );

                //insert data pajak ke tabel transaction
                Transactions::updateOrCreate(
                    ['no_projek' => $noProjek, 'categorie_finance_id' => $idCategoriePajak],
                    [
                        'categorie_finance_id' => $idCategoriePajak,
                        'no_projek' => $noProjek,
                        'name' => $nameTransaction,
                        'description' => $descriptionTransaction,
                        'total' => $pajak,
                        'date_transaction' => $date,
                    ]
                );
            }
        });

        static::deleting(function ($project) {
            $noProjek = $project->no_projek;

            Transactions::where('no_projek', $noProjek)->delete();
        });
    }
}
