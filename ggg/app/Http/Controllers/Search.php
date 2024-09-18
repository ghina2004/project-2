<?php

namespace App\Http\Controllers;

use App\Models\const_trip;
use App\Models\hotel;
use App\Models\optionaljourny;
use App\Models\restaurant;
use App\Models\ticket;
use App\Models\trip_schadual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Laravel\Scout\Searchable;
use PhpParser\Node\Expr\New_;

class Search extends Controller
{



    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json(['message' => 'Enter What You Want To Search For'], 201);
        }

        $models = [
            optionaljourny::class,
            const_trip::class,
            ticket::class,
            restaurant::class,
            hotel::class,
            trip_schadual::class,
        ];

        $resultsByModel = [];
        $anyResultsFound = false;

        foreach ($models as $model) {
            $modelInstance = resolve($model);
            $table = $modelInstance->getTable();
            $columns = Schema::getColumnListing($table);

            $results = $model::where(function ($queryBuilder) use ($query, $columns, $table) {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        try {
                            $columnType = Schema::getColumnType($table, $column);
                            if ($columnType === 'string' || $columnType === 'text') {
                                $queryBuilder->orWhere($column, 'LIKE', "%$query%");
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
            })->get();

            $filteredResults = $results->filter(function ($item) use ($query, $columns) {
                foreach ($columns as $column) {
                    if (is_string($item->$column)) {
                        similar_text(strtolower($query), strtolower($item->$column), $percent);
                        if ($percent >= 80) {
                            return true;
                        }
                    }
                }
                return false;
            });

            $modelName = class_basename($model);
            if (!$filteredResults->isEmpty()) {
                $resultsByModel[$modelName] = $filteredResults->values(); // إزالة الترقيم
                $anyResultsFound = true;
            } else {
                $resultsByModel[$modelName] = [];
            }
        }

        if (!$anyResultsFound) {
            return response()->json(['message' => 'No matching results found '], 404);
        }

        return response()->json(['results' => $resultsByModel], 200);
    }
} 
