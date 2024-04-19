<?php

namespace NadzorServera\Skijasi\Helpers;

use Exception;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use NadzorServera\Skijasi\Models\DataType;
use NadzorServera\Skijasi\Models\Permission;
use NadzorServera\Skijasi\Models\UserRole;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use NadzorServera\Skijasi\Models\User;

class GetData
{
    protected static $cjelinaMapping = [
        'Opći dio' => [4, 7, 127, 157, 197, 184],
        'I. dio specijalnosti' => [1, 5, 125, 155, 195],
        'II. dio specijalnosti' => [2, 6, 126, 156, 196],
    ];

    protected static $cjelinaMapping2 = [
        'Teoretski dio' => [184],
    ];

    public static function getStatusMessagesForUsers($userIds)
    {
        $statusMessages = [];
        $edukacijskiProgramIds = [];
    
        foreach ($userIds as $userId) {
            // Fetch user data
            $user = DB::table('su_clanovi')->find($userId);
    
            $edukacijskiProgrami = DB::table('su_clanoviedukacijskipodaci')
                ->where('idmember', $user->id)
                ->get();
    
            $statusMessages[$userId] = [];
            $edukacijskiProgramIds[$userId] = [];
    
            foreach ($edukacijskiProgrami as $edukacijskiProgram) {
                $isTrenerSkijanja = ($edukacijskiProgram && $edukacijskiProgram->idedukacijskogprograma == 5);
    
                // Fetch related data
                $edukacijskeGrupe = DB::table('su_edukacijskagrupasegmentclan')
                    ->join('su_edukacijskagrupa', 'su_edukacijskagrupasegmentclan.idedukacijskegupe', '=', 'su_edukacijskagrupa.id')
                    ->where('su_edukacijskagrupasegmentclan.idmember', $userId)
                    ->select('su_edukacijskagrupa.*')
                    ->get();
    
                    Log::info("Fetched edukacijske grupe for user ID: $userId, count: " . $edukacijskeGrupe->count());


    
                // Fetch ispiti based on the condition
                $ispiti = collect();
    
                foreach ($edukacijskeGrupe as $grupa) {
                    $grupaIspiti = DB::table('su_ispiti')
                        ->join('su_edukacijskagrupasegmentclan', 'su_ispiti.idedukacijskogsegmentaclana', '=', 'su_edukacijskagrupasegmentclan.id')
                        ->where('su_edukacijskagrupasegmentclan.idedukacijskegupe', $grupa->id)
                        ->where('su_edukacijskagrupasegmentclan.idmember', $userId)
                        ->select('su_ispiti.*', 'su_ispiti.idsegmenta AS idsegmenta', 'su_edukacijskagrupasegmentclan.idsegmenta AS clan_idsegmenta')
                        ->get();
    
                    Log::info("Fetched ispiti for grupa ID: $grupa->id, count: " . $grupaIspiti->count());
    
                    $ispiti = $ispiti->merge($grupaIspiti);
                }
    
                Log::info("Total ispiti fetched for user ID: $userId, count: " . $ispiti->count());
    
                // Filter the $ispiti collection based on the educational program
                $programId = $edukacijskiProgram ? $edukacijskiProgram->idedukacijskogprograma : null;
                $ispitiFiltred = $ispiti->filter(function ($ispit) use ($programId) {
                    return self::getProgramIdForIspit($ispit) == $programId;
                });
    
                Log::info("Ispiti filtered for program ID: $programId, count: " . $ispitiFiltred->count());
    
                $statusMessage = self::calculateStatusMessage($ispitiFiltred, $edukacijskiProgram, $isTrenerSkijanja);
    
    
                // Store status message with the corresponding educational program ID
                $edukacijskiProgramId = $edukacijskiProgram ? $edukacijskiProgram->idedukacijskogprograma : null;
                $edukacijskiProgramName = self::getEdukacijskiProgramName($edukacijskiProgramId);
    
                $statusMessages[$userId][$edukacijskiProgramId] = $statusMessage;
                $edukacijskiProgramIds[$userId][] = $edukacijskiProgramName;
    
                Log::info('Educational program fetched for user ID: ' . $userId . ', Program ID: ' . $edukacijskiProgramId . ', Program Name: ' . $edukacijskiProgramName);
                Log::info('Status Message: ' . $statusMessage);
                Log::info('Educational Program Name: ' . $edukacijskiProgramName);
            }
        }
    
        return ['statusMessages' => $statusMessages, 'edukacijskiProgramIds' => $edukacijskiProgramIds];
    }
    

    protected static function getEdukacijskiProgramName($edukacijskiProgramId)
    {
        switch ($edukacijskiProgramId) {
            case 1:
            case 3:
            case 4:
                return "Učitelj Skijanja";
            case 2:
            case 6:
                return "Učitelj daskanja na snijegu";
            case 5:
                return "Trener Skijanja";
            default:
                return $edukacijskiProgramId;
        }
    }

    protected static function calculateStatusMessage($ispiti, $edukacijskiProgram, $isTrenerSkijanja)
    {
        $now = new \DateTime();
        $status = "U tijeku školovanje"; // Default status

        $allCjelinaCompleted = true;
        $generalStatus = "";
        $failedCount = 0;
        $anyValidPopravciDateFound = false;

        // Group ispiti by cjelina based on the educational program
        $ispitiByCjelina = $isTrenerSkijanja
            ? self::groupIspitiByCjelina($ispiti, static::$cjelinaMapping2)
            : self::groupIspitiByCjelina($ispiti, static::$cjelinaMapping);

        // Check if there are no cjelina present
        if (count($ispitiByCjelina) === 0) {
            \Log::info('NEMA CJELINA ');
            return $status; // Return "U tijeku školovanje" if no cjelina are present
        }

        // Check if all cjelina are present and have all ispiti with grade > 1 or idadmitmakeupseg is not null or has a higher grade
        $cjelinaNames = $isTrenerSkijanja
            ? array_keys(static::$cjelinaMapping2)
            : ['Opći dio', 'I. dio specijalnosti', 'II. dio specijalnosti'];

        $hasAllCjelinasPassed = true;

        foreach ($cjelinaNames as $cjelinaName) {
            $cjelinaIspiti = $ispitiByCjelina[$cjelinaName] ?? [];
            \Log::info('CJELINA ISPITI EMPTY: ', (array)$cjelinaIspiti);

            if (empty($cjelinaIspiti)) {
                $hasAllCjelinasPassed = false;
                break;
            }

            $cjelinaCompleted = true;

            foreach ($cjelinaIspiti as $ispit) {
                $highestGrade = self::getHighestGradeForSegment($ispit->idsegmenta, $ispiti);

                if (
                    ($ispit->ocjenaispita && $ispit->ocjenaispita <= 1 && !$highestGrade) &&
                    $ispit->idadmitmakeupseg === null
                ) {
                    $cjelinaCompleted = false;
                    break;
                }
            }

            if (!$cjelinaCompleted) {
                \Log::info('Cjelina not completed: ' . $cjelinaName);
                $hasAllCjelinasPassed = false;
                break;
            }
        }

        if ($hasAllCjelinasPassed) {
            \Log::info('ZAVRSENO SKOLOVANJE ');
            return "Završeno školovanje";
        }
    
        // Rest of your existing logic
        $hasHigherGrade = function ($ispitId) use ($ispiti) {
            return $ispiti->contains(function ($ispit) use ($ispitId) {
                return $ispit->idsegmenta === $ispitId && $ispit->ocjenaispita && $ispit->ocjenaispita > 1;
            });
        };
    
        foreach ($ispitiByCjelina as $cjelinaIspiti) {
            foreach ($cjelinaIspiti as $ispit) {
                $ocjenaispita = intval($ispit->ocjenaispita);
if (is_int($ispit->ocjenaispita) && $ispit->ocjenaispita == 1 && !$hasHigherGrade($ispit->idsegmenta)) {
                    $failedCount++;
                    // Mark all cjelina as not completed if any ispit requires "Popravci"
                    $allCjelinaCompleted = false;
                }
            }
        }
    
        if ($failedCount >= 3) {
            $generalStatus = "Pad";
        }
    
        // Handle "Prošlo 5.godina od I.specijalnosti" if applicable
     //   $ispitiI = $ispitiByCjelina['I. dio specijalnosti'] ?? [];
      //  $ispitiI = array_filter($ispitiI, function ($ispit) use ($hasHigherGrade) {
      //      return $ispit->ocjenaispita === 1 && !$hasHigherGrade($ispit->idsegmenta) && $ispit->datumispita !== null;
    //    });
    
        if (!empty($ispitiI)) {
            $oldestIspitDate = null;
            foreach ($ispitiI as $ispit) {
                // Check if datumispita is not empty or null
                if (!empty($ispit->datumispita)) {
                    $currentDate = new \DateTime($ispit->datumispita);
                    if ($oldestIspitDate === null || $currentDate < $oldestIspitDate) {
                        $oldestIspitDate = $currentDate;
                    }
                }
            }
        
            // If oldestIspitDate is still null, it means all datumispita values were empty or null
            if ($oldestIspitDate !== null) {
                $yearsSinceOldestIspit = $oldestIspitDate->diff($now)->y;
        
                if ($yearsSinceOldestIspit > 5) {
                    return "Prošlo 5.godina od I.specijalnosti";
                }
            }
        }
        
    
        if (!$allCjelinaCompleted && $anyValidPopravciDateFound) {
            return $generalStatus; // Return the time-based status if a valid "Popravci" date is found
        } elseif (!$allCjelinaCompleted && $generalStatus === "Pad") {
            return $generalStatus; // Return "Pad" if there are 3 or more failed ispiti
        } elseif (!$allCjelinaCompleted) {
            return "Popravci"; // Return "Popravci" if any are needed, even without a valid date
        }
        else if ($hasAllCjelinasPassed) {
            return "Završeno školovanje";
        }
    
        return $status; // "U tijeku školovanje" as a fallback
    }

    protected static function getHighestGradeForSegment($idsegmenta, $ispiti)
{
    $ispitiForSegment = $ispiti->where('idsegmenta', $idsegmenta);
    $highestGrade = $ispitiForSegment->max('ocjenaispita');

    return $highestGrade > 1 ? $highestGrade : null;
}


protected static function groupIspitiByCjelina($ispiti, $cjelinaMapping)
{
    $ispitiByCjelina = [];

    foreach ($ispiti as $ispit) {
        $cjelinaText = self::getCjelinaText($ispit, $cjelinaMapping);
        Log::info("Cjelina text for ispit: ", (array)$ispit, " is: $cjelinaText");

        if (!isset($ispitiByCjelina[$cjelinaText])) {
            $ispitiByCjelina[$cjelinaText] = [];
        }
        $ispitiByCjelina[$cjelinaText][] = $ispit;
    }

    Log::info("Ispiti grouped by cjelina: ", $ispitiByCjelina);

    return $ispitiByCjelina;
}

protected static function getCjelinaText($ispit, $cjelinaMapping)
{
    $parentsegid = self::getParentSegIdForIspit($ispit);
    Log::info("Cjelina found for parentsegid:", (array)$ispit, " parentsegid: $parentsegid");

    foreach ($cjelinaMapping as $cjelinaName => $segmentIds) {
        Log::info("Checking cjelina: $cjelinaName, segmentIds: ", $segmentIds);
        if (in_array($parentsegid, $segmentIds)) {
            Log::info("Cjelina found: $cjelinaName for parentsegid: $parentsegid");
            return $cjelinaName;
        }
    }

    Log::info("Cjelina not found for parentsegid: $parentsegid");
    return 'Ostalo';
}

    protected static function getParentSegIdForIspit($ispit)
    {
        $segment = DB::table('su_edukacijskisegmenti')
            ->where('parentsegid', $ispit->clan_idsegmenta)
            ->first();
    
        return $segment ? $segment->parentsegid : null;
    }

    protected static function getProgramIdForIspit($ispit)
    {
        $segment = DB::table('su_edukacijskisegmenti')
            ->where('id', $ispit->clan_idsegmenta)
            ->first();
    
        if ($segment) {
            return $segment->idedukacijskogprograma;
        } else {
            // If no segment is found, try to get the program ID from the parent segment
            $parentSegment = DB::table('su_edukacijskisegmenti')
                ->where('parentsegid', $ispit->clan_idsegmenta)
                ->first();
    
            return $parentSegment ? $parentSegment->idedukacijskogprograma : null;
        }
    }






    public static function serverSideWithModel($data_type, $builder_params, $only_data_soft_delete = false)
    {
        $fields_data_identifier = collect($data_type->dataRows)->where('type', 'data_identifier')->pluck('field')->all();
        $fields = collect($data_type->dataRows)->where('browse', 1)->pluck('field')->all();
        $ids = collect($data_type->dataRows)->where('field', 'id')->pluck('field')->all();
        $fields = array_merge($fields, $ids, $fields_data_identifier);
        $data_rows = collect($data_type->dataRows);

        $model = app($data_type->model_name);
        $limit = $builder_params['limit'];
        $order_field = $builder_params['order_field'];
        $order_direction = $builder_params['order_direction'];
        $filter_key = $builder_params['filter_key'];
        $filter_operator = $builder_params['filter_operator'];
        $filter_value = $builder_params['filter_value'];
        $field_other_relation = [];

       // $filter_dateendmember = $builder_params['filter_dateendmember'];


        $is_roles = false;
        $field_identify_related_user = null;
        $is_public = 0;
        $roles_can_see_all_data = [];

        $permissions = Permission::where('key', 'browse_'.$data_type->name)->where('table_name', $data_type->name)->select('roles_can_see_all_data', 'field_identify_related_user', 'is_public')->first();

        $field_identify_related_user = $permissions ? $permissions['field_identify_related_user'] : null;

        $is_public = $permissions ? $permissions['is_public'] : 0;

        $roles_can_see_all_data = json_decode($permissions) ? json_decode($permissions['roles_can_see_all_data']) : [];

        if ($is_public !== 1) {
            if (! empty(auth()->user())) {
                $user_roles = auth()->user()->roles;

                foreach ($user_roles as $key => $user_role) {
                    $is_roles = in_array($user_role->name, $roles_can_see_all_data);
                }
            }
        } else {
            $all_user_roles = UserRole::with(['user', 'role'])->get();
            $users = json_decode($all_user_roles);

            foreach ($users as $key => $user) {
                $user_roles[] = $user->role;
                foreach ($user_roles as $key => $user_role) {
                    $is_roles = isset($user_role->name, $roles_can_see_all_data);
                }
            }
        }

        $is_field = in_array($field_identify_related_user, array_merge($fields, $fields_data_identifier));

        foreach ($data_rows as $key => $data_row) {
            if (isset($data_row['relation']) && $data_row['relation']['relation_type'] != 'belongs_to') {
                $field_other_relation[] = $data_row['field'];
            }
        }

        

        $fields = array_diff(array_merge($fields, $ids, $fields_data_identifier), $field_other_relation);

        $records = [];
        $query = $model::query()->select($fields);

        // Check for specific table (e.g., 'skijasi_users') before applying user_type filter
if ($data_type->name == 'skijasi_users') {
    $query->where('user_type', 'Hzuts član'); 
}

        if (! $is_roles) {
            if ($is_field) {
                $query = $model::query()->select($fields)->where($field_identify_related_user, auth()->user()->id);
            }
        }

        // soft delete implement
        $is_soft_delete = $data_type->is_soft_delete;
        if ($is_soft_delete) {
            if ($only_data_soft_delete) {
                $query->whereNotNull('deleted_at');
            } else {
                $query->whereNull('deleted_at');
            }
        }
        // end
// ... [Previous Code]
//if ($filter_dateendmember) {
    if ($data_type->name == 'skijasi_users') {
        // if the 'user_type' column exists, then apply the condition
        $query->where(function ($query) {
            // Your existing constraints inside the closure
            // $query->whereNull('dateendmember');
            //  ->orWhere('dateendmember', '');
        })->where('user_type', 'Hzuts član');
    }
//}



// If there's a filter value, adjust the query
if ($filter_value) {
    $searchTerms = explode(' ', $filter_value);  // Split the filter_value into individual terms
    
    foreach ($searchTerms as $term) {
        $query->where(function($query) use ($term, $fields) {
            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', "%{$term}%");
            }
        });
    }
}




// ... [Rest of the Code]

        if ($order_field) {
            $query->orderBy($order_field, $order_direction);
        }
        $data = $query->paginate($limit ? $limit : 10);
        $collection = $data->getCollection();
        foreach ($collection as $row) {
            $class = new ReflectionClass(get_class($row));
            $class_methods = $class->getMethods();
            $record = $row;
            foreach ($class_methods as $class_method) {
                if ($class_method->class == $class->name) {
                    try {
                        $record->{$class_method->name} = json_decode(json_encode($row->{$class_method->name}));
                    } catch (Exception $e) {
                        // $record->{$class_method->name} = json_decode(json_encode($row->{$class_method->name}()));
                    }
                }
            }
            $records[] = self::getRelationData($data_type, $record);
        }
        $data->setCollection(collect($records));

        return $data;
    }

    public static function clientSideWithModel($data_type, $builder_params, $only_data_soft_delete = false)
    {
        $fields_data_identifier = collect($data_type->dataRows)->where('type', 'data_identifier')->pluck('field')->all();
        $fields = collect($data_type->dataRows)->where('browse', 1)->pluck('field')->all();
        $ids = collect($data_type->dataRows)->where('field', 'id')->pluck('field')->all();
        $data_rows = collect($data_type->dataRows);
        $field_other_relation = [];

        $is_roles = false;
        $field_identify_related_user = null;
        $is_public = 0;
        $roles_can_see_all_data = [];

        $permissions = Permission::where('key', 'browse_'.$data_type->name)->where('table_name', $data_type->name)->select('roles_can_see_all_data', 'field_identify_related_user', 'is_public')->first();

        $field_identify_related_user = $permissions ? $permissions['field_identify_related_user'] : null;

        $is_public = $permissions ? $permissions['is_public'] : 0;

        $roles_can_see_all_data = json_decode($permissions) ? json_decode($permissions['roles_can_see_all_data']) : [];

        if ($is_public !== 1) {
            if (! empty(auth()->user())) {
                $user_roles = auth()->user()->roles;

                foreach ($user_roles as $key => $user_role) {
                    $is_roles = in_array($user_role->name, $roles_can_see_all_data);
                }
            }
        } else {
            $all_user_roles = UserRole::with(['user', 'role'])->get();
            $users = json_decode($all_user_roles);

            foreach ($users as $key => $user) {
                $user_roles[] = $user->role;
                foreach ($user_roles as $key => $user_role) {
                    $is_roles = isset($user_role->name, $roles_can_see_all_data);
                }
            }
        }

           // Check for specific table (e.g., 'skijasi_users') before applying user_type filter
if ($data_type->name == 'skijasi_users') {
    $query->where('user_type', 'Hzuts član'); 
}

        $is_field = in_array($field_identify_related_user, array_merge($fields, $fields_data_identifier));

        foreach ($data_rows as $key => $data_row) {
            if (isset($data_row['relation']) && $data_row['relation']['relation_type'] != 'belongs_to') {
                $field_other_relation[] = $data_row['field'];
            }
        }

        $fields = array_diff(array_merge($fields, $ids, $fields_data_identifier), $field_other_relation);

        $model = app($data_type->model_name);
        $order_field = $builder_params['order_field'];
        $order_direction = $builder_params['order_direction'];

        $records = [];

        if ($order_field) {
            $data = $model::query()->select($fields)->orderBy($order_field, $order_direction);
            if (! $is_roles) {
                if ($is_field) {
                    $data = $model::query()->select($fields)->orderBy($order_field, $order_direction)->where($field_identify_related_user, auth()->user()->id);
                }
            }
        } else {
            $data = $model::query()->select($fields);
            if (! $is_roles) {
                if ($is_field) {
                    $data = $model::query()->select($fields)->where($field_identify_related_user, auth()->user()->id);
                }
            }
        }
        // soft delete implement
        $is_soft_delete = $data_type->is_soft_delete;
        if ($is_soft_delete) {
            if ($only_data_soft_delete) {
                $data = $data->whereNotNull('deleted_at');
            } else {
                $data = $data->whereNull('deleted_at');
            }
        }
        // end
        $data = $data->get();

        foreach ($data as $row) {
            $class = new ReflectionClass(get_class($row));
            $class_methods = $class->getMethods();

            $record = $row;
            foreach ($class_methods as $class_method) {
                if ($class_method->class == $class->name) {
                    try {
                        $record->{$class_method->name} = json_decode(json_encode($row->{$class_method->name}));
                    } catch (Exception $e) {
                        // $record->{$class_method->name} = json_decode(json_encode($row->{$class_method->name}()));
                    }
                }
            }
            $records[] = self::getRelationData($data_type, $record);
        }

        // add field data form table polymoriphims
        $records = collect($records)->map(function ($record) use ($data_rows) {
            foreach ($data_rows as $index => $data_row) {
                if (isset($data_row->relation) && $data_row->relation['relation_type'] == 'belongs_to_many') {
                    $table_manytomany = $data_row['field'];
                    $data_relation = DB::table($table_manytomany)
                        ->get();
                    $record->$table_manytomany = $data_relation;
                }
            }

            return $record;
        });

        $data = [];
        foreach ($records as $row) {
            $data[] = self::getRelationData($data_type, $row);
        }

        $entities['data'] = $data;
        $entities['total'] = count($data);

        return $entities;
    }








    public static function serverSideWithQueryBuilder($data_type, $builder_params, $only_data_soft_delete = false)
    {
        $fields_data_identifier = collect($data_type->dataRows)->where('type', 'data_identifier')->pluck('field')->all();
        $fields = collect($data_type->dataRows)->where('browse', 1)->pluck('field')->all();
        $ids = collect($data_type->dataRows)->where('field', 'id')->pluck('field')->all();
        $fields = array_merge($fields, $ids, $fields_data_identifier);
        $data_rows = collect($data_type->dataRows);
        $field_other_relation = [];

        $limit = $builder_params['limit'];
        $page = $builder_params['page'];
        $order_field = $builder_params['order_field'];
        $order_direction = $builder_params['order_direction'];
        $filter_key = $builder_params['filter_key'];
        $filter_operator = $builder_params['filter_operator'];
        $filter_value = $builder_params['filter_value'];

        // za filter kraja clanstva
        $activeFilter = $builder_params['activeFilter'] ?? null;



        $is_roles = false;
        $field_identify_related_user = null;
        $is_public = 0;
        $roles_can_see_all_data = [];

        $permissions = Permission::where('key', 'browse_'.$data_type->name)->where('table_name', $data_type->name)->select('roles_can_see_all_data', 'field_identify_related_user', 'is_public')->first();

        $field_identify_related_user = $permissions ? $permissions['field_identify_related_user'] : null;

        $is_public = $permissions ? $permissions['is_public'] : 0;

        $roles_can_see_all_data = json_decode($permissions) ? json_decode($permissions['roles_can_see_all_data']) : [];

        if ($is_public !== 1) {
            if (! empty(auth()->user())) {
                $user_roles = auth()->user()->roles;

                foreach ($user_roles as $key => $user_role) {
                    $is_roles = in_array($user_role->name, $roles_can_see_all_data);
                }
            }
        } else {
            $all_user_roles = UserRole::with(['user', 'role'])->get();
            $users = json_decode($all_user_roles);

            foreach ($users as $key => $user) {
                $user_roles[] = $user->role;
                foreach ($user_roles as $key => $user_role) {
                    $is_roles = isset($user_role->name, $roles_can_see_all_data);
                }
            }
        }

        foreach ($data_rows as $key => $data_row) {
            if (isset($data_row['relation']) && $data_row['relation']['relation_type'] != 'belongs_to') {
                $field_other_relation[] = $data_row['field'];
            }
        }

        $is_field = in_array($field_identify_related_user, array_merge($fields, $fields_data_identifier));
        $fields = array_diff(array_merge($fields, $ids, $fields_data_identifier), $field_other_relation);
        $query = DB::table($data_type->name)->select($fields);

        if (! $is_roles) {
            if ($is_field) {
                $query = DB::table($data_type->name)->select($fields)->where($field_identify_related_user, auth()->user()->id);
            }
        }

        // soft delete implement
        $is_soft_delete = $data_type->is_soft_delete;
        if ($is_soft_delete) {
            if ($only_data_soft_delete) {
                $query = $query->whereNotNull('deleted_at');
            } else {
                $query = $query->whereNull('deleted_at');
            }
        }
        // end


        

          if ($data_type->name == 'skijasi_users') {
    
        


            switch ($activeFilter) {
                case 'hzuts':
                    $query->where('user_type', 'Hzuts član');
                    break;
                case 'neaktivni':
                    $query->whereNotNull('dateendmember');
                    break;
                case 'obicankorisnik':
                    $query->where('user_type', 'Običan Korisnik');
                    break;
                default:
                $query->where('user_type', 'Hzuts član');
                break;
            }
        
    


        
    }




// If there's a filter value, adjust the query
if ($filter_value) {
    $searchTerms = explode(' ', $filter_value);  // Split the filter_value into individual terms
    
    foreach ($searchTerms as $term) {
        $query->where(function($query) use ($term, $fields) {
            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', "%{$term}%");
            }
        });
    }
}



// ... [Rest of the Code]

        $paginate = $query;
        $total = $query->count();

        if ($limit) {
            $paginate->limit($limit);
        }
        if ($page) {
            $paginate->offset(($page - 1) * $limit);
        }
        if ($order_field) {
            $paginate->orderBy($order_field, $order_direction);
        }
        $data = $paginate->get();

        $collection = $data;

        $records = [];
        foreach ($collection as $row) {
            $records[] = self::getRelationData($data_type, $row);
        }


        if ($data_type->name == 'su_clanovi') {
            foreach ($records as $key => $record) {
                $result = self::getStatusMessagesForUsers([$record->id]);
                $sustatusclanova = $result['statusMessages'][$record->id] ?? [];
                $edukacijskiProgramIds = $result['edukacijskiProgramIds'][$record->id] ?? [];
        
                $records[$key]->sustatusclanova = $sustatusclanova;
                $records[$key]->upisanieduprogrami = $edukacijskiProgramIds;

                Log::info('Record ID: ' . $record->id);
                Log::info('Status Messages: ' . print_r($sustatusclanova, true));
                Log::info('Edukacijski Program IDs: ' . print_r($edukacijskiProgramIds, true));
            }
        }



        if ($data_type->name == 'skijasi_users') {
        $trainerStatusLabels = self::getTrainerStatusLabels();

        foreach ($records as $key => $record) {
            // Fetch and calculate payment status
            $paymentData = self::fetchPaymentDataForMember($record->id);
            $statusPlacanja = self::calculatePaymentStatus($paymentData);
            $records[$key]->statusPlacanja = $statusPlacanja;
        
            // Fetch and calculate status data for 'statusString' and 'statusAktivan'
            $statusData = self::fetchStatusDataForMember($record->id); // Fetch status data for the member
            $records[$key]->statusString = self::calculateStatusString($statusData, $trainerStatusLabels);
           
            // Fetch licence data
            $licenceData = self::fetchLicenceDataForMember($record->id);
            $records[$key]->licenceData = $licenceData;

            $statusAktivanData = self::calculateStatusAktivan($statusData);
            $records[$key]->statusAktivan = $statusAktivanData['status'];
            $records[$key]->endstatusdate = $statusAktivanData['endstatusdate'];


            $isiaData = self::fetchISIAbroj($record->id);
            $records[$key]->isiaBroj = $isiaData;
          
        }  
    
    }
        


        $data = collect($records);

        $entities['data'] = $data;
        $entities['from'] = $page ? (($page - 1) * $limit) + 1 : 0;
        $entities['to'] = 0;
        if ($page) {
            $entities['to'] = count($data) > $limit ? $page * $limit : (($page - 1) * $limit) + count($data);
        }
        $entities['total'] = $total;


        

        return $entities;
    }


    public static function fetchLicenceDataForMember($idMember) {
        $licenceData = DB::table('tbl_licence')
                         ->where('idmember', $idMember)
                         ->where('aktivna', 1)
                         ->get(['nazivlicence']);

        return $licenceData;
    }
    


    public static function fetchStatusDataForMember($idMember) {
        // Fetch the status data for the given member ID from your database
        $statusData = DB::table('tbl_member_status')
                         ->where('idmember', $idMember)
                         ->get();
    
        return $statusData;
    }

    public static function fetchISIAbroj($idMember) {
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
    
        $latestIsiaRecord = DB::table('tbl_isia_member')
                              ->where('idmember', $idMember)
                              ->whereIn('isiayear', [$currentYear, $nextYear])
                              ->orderBy('isiayear', 'desc')
                              ->first();
    
        return $latestIsiaRecord;
    }
    
    
    public static function calculateStatusString($statusData, $trainerStatusLabels) {
        $statusLabels = collect($statusData)
                        ->filter(function ($item) {
                            return $item->statusdefault === 1;
                        })
                        ->map(function ($item) use ($trainerStatusLabels) {
                            return $trainerStatusLabels[$item->trainerstsid] ?? null;
                        })
                        ->filter()
                        ->implode(', ');
    
        return $statusLabels;
    }
// Example of fetching trainer status labels
public static function getTrainerStatusLabels() {
    $trainerStatuses = DB::table('trainersts')->get(); 
    $labels = [];
    foreach ($trainerStatuses as $status) {
        $labels[$status->id] = $status->cardscro ?? 'cardscro'; 
        
    }
    return $labels;
}
    


    
public static function calculateStatusAktivan($statusData) {
    $today = new \DateTime(); // Use \DateTime for global namespace
    $today->setTime(0, 0, 0); // Set to start of day

    $statusAktivan = 'Istekla licenca';
    $latestEndDate = null;
    $idevent = null;

    $latestEventDate = null; 

    foreach ($statusData as $item) {
        if (isset($item->endstatusdate)) {
            $endDate = new \DateTime($item->endstatusdate); // Use \DateTime here as well
            $endDate->setTime(0, 0, 0); // Set to start of day

            if ($endDate >= $today) {
                if ($latestEndDate === null || $endDate > new \DateTime($latestEndDate)) {
                    $latestEndDate = $item->endstatusdate; // Always update to the latest end date
                    $idevent = $item->idevent; // Update idevent corresponding to the latest end date
                    
                $statusAktivan = 'Aktivan';
                $latestEndDate = $item->endstatusdate;
                $idevent = $item->idevent; 
               // break; // If one active status is found, no need to check further
            }         }
            else if ($endDate < $today) {
                $latestEndDate = $item->endstatusdate;
                $idevent = $item->idevent; 
             
            }

       
        }
    }

    return ['status' => $statusAktivan, 'endstatusdate' => $latestEndDate,  'idevent' => $idevent];
}





    


    public static function fetchPaymentDataForMember($idMember) {
        // Fetch the payment data along with trainerstsid for the given member ID
        $paymentData = DB::table('tbl_payments')
                         ->join('tbl_member_status', 'tbl_payments.idmember', '=', 'tbl_member_status.idmember')
                         ->where('tbl_payments.idmember', $idMember)
                         ->select('tbl_payments.*', 'tbl_member_status.trainerstsid')
                         ->get();
        return $paymentData;
    }
    

    public static function calculatePaymentStatus($paymentData) {
        // Initialize status variables
        $anyPaid = false;
        $anyPartialPaid = false;
        $allUnpaid = true;

              // Special condition for trainerstsid = 10
    $specialStatus = collect($paymentData)->contains('trainerstsid', 10);

        if ($specialStatus) {
            return "Sve plaćeno";
        }
    
        // Check if there are no payments
        if (empty($paymentData)) {
            return "Nema plaćanja";
        }

    

    
        // Iterate over each payment record to determine the status
        foreach ($paymentData as $payment) {
            if ($payment->paidstatus || $payment->paymentdiscard || $payment->paymentforgive) {
                $anyPaid = true;
            }
            if ($payment->partialpaid) {
                $anyPartialPaid = true;
            }
            if (!$payment->paidstatus && !$payment->partialpaid && !$payment->paymentdiscard && !$payment->paymentforgive) {
                $allUnpaid = false;
            }
            
        }
    
        // Determine final status based on the flags
        if ($anyPartialPaid) {
            return "Nije plaćeno";
        }
        if ($anyPaid && $allUnpaid) {
            return "Sve plaćeno";
        }
        if (!$anyPaid && !$allUnpaid) {
            return "Nije plaćeno";
        }
        if ($anyPaid) {
            return "Nije plaćeno";
        }
        if ($allUnpaid) {
            return "Nema plaćanja";
        }
        return "Greska";
    }
    




    public static function clientSideWithQueryBuilder($data_type, $builder_params, $only_data_soft_delete = false)
    {
        $fields_data_identifier = collect($data_type->dataRows)->where('type', 'data_identifier')->pluck('field')->all();
        $data_rows = collect($data_type->dataRows);
        $fields = $data_rows->where('browse', 1)->pluck('field')->all();
        $ids = $data_rows->where('field', 'id')->pluck('field')->all();
        $field_other_relation = [];
        $is_roles = false;
        $field_identify_related_user = null;
        $is_public = 0;
        $roles_can_see_all_data = [];

        $permissions = Permission::where('key', 'browse_'.$data_type->name)->where('table_name', $data_type->name)->select('roles_can_see_all_data', 'field_identify_related_user', 'is_public')->first();

        $field_identify_related_user = $permissions ? $permissions['field_identify_related_user'] : null;

        $is_public = $permissions ? $permissions['is_public'] : 0;

        $roles_can_see_all_data = json_decode($permissions) ? json_decode($permissions['roles_can_see_all_data']) : [];

        if ($is_public !== 1) {
            if (! empty(auth()->user())) {
                $user_roles = auth()->user()->roles;

                foreach ($user_roles as $key => $user_role) {
                    $is_roles = in_array($user_role->name, $roles_can_see_all_data);
                }
            }
        } else {
            $all_user_roles = UserRole::with(['user', 'role'])->get();
            $users = json_decode($all_user_roles);

            foreach ($users as $key => $user) {
                $user_roles[] = $user->role;
                foreach ($user_roles as $key => $user_role) {
                    $is_roles = isset($user_role->name, $roles_can_see_all_data);
                }
            }
        }

        foreach ($data_rows as $key => $data_row) {
            if (isset($data_row['relation']) && $data_row['relation']['relation_type'] != 'belongs_to') {
                $field_other_relation[] = $data_row['field'];
            }
        }

        $is_field = in_array($field_identify_related_user, array_merge($fields, $fields_data_identifier));
        $fields = array_diff(array_merge($fields, $ids, $fields_data_identifier), $field_other_relation);
        $order_field = $builder_params['order_field'];
        $order_direction = $builder_params['order_direction'];

        if ($order_field) {
            $records = DB::table($data_type->name)->select($fields)->orderBy($order_field, $order_direction);
        } else {
            $records = DB::table($data_type->name)->select($fields);
        }

        // soft delete implement
        $is_soft_delete = $data_type->is_soft_delete;
        if ($is_soft_delete) {
            if ($only_data_soft_delete) {
                $records = $records->whereNotNull('deleted_at');
            } else {
                $records = $records->whereNull('deleted_at');
            }
        }
        // end

        $records = $records->get()->map(function ($record) use ($data_rows) {
            foreach ($data_rows as $index => $data_row) {
                if ($data_row->type == 'upload_image_multiple') {
                    if (isset($record->{$data_row->field})) {
                        $upload_image_multiples = json_decode($record->{$data_row->field}, true);
                        if (isset($upload_image_multiples)) {
                            $upload_image_multiples = collect($upload_image_multiples)->map(function ($upload_image_multiple) {
                                if (config('lfm.should_create_thumbnails') == true) {
                                    $put_thumbs = config('lfm.thumb_folder_name');
                                    $upload_image_multiple = explode('/', $upload_image_multiple);
                                    $file_name = $upload_image_multiple[count($upload_image_multiple) - 1];
                                    $upload_image_multiple[count($upload_image_multiple) - 1] = $put_thumbs;
                                    $upload_image_multiple[] = $file_name;
                                    $upload_image_multiple = join('/', $upload_image_multiple);
                                }
                                $asset = asset('storage/'.$upload_image_multiple);

                                return $asset;
                            });
                            $upload_image_multiples = implode(',', json_decode($upload_image_multiples));
                        }
                        $record->{$data_row->field} = $upload_image_multiples;
                    }
                } elseif ($data_row->type == 'upload_image') {
                    if (isset($record->{$data_row->field})) {
                        $upload_image = $record->{$data_row->field};

                        if (isset($upload_image)) {
                            if (str_contains($upload_image, 'http')) {
                                $upload_image = $upload_image;
                            } else {
                                if (config('lfm.should_create_thumbnails') == true) {
                                    $put_thumbs = config('lfm.thumb_folder_name');
                                    $upload_image = explode('/', $upload_image);
                                    $file_name = $upload_image[count($upload_image) - 1];
                                    $upload_image[count($upload_image) - 1] = $put_thumbs;
                                    $upload_image[] = $file_name;
                                    $upload_image = join('/', $upload_image);
                                }
                                $upload_image = asset('storage/'.$upload_image);
                            }
                            $record->{$data_row->field} = $upload_image;
                        }
                    }
                } elseif (isset($data_row->relation) && $data_row->relation['relation_type'] == 'belongs_to_many') {
                    $table_manytomany = $data_row['field'];
                    $data_relation = DB::table($table_manytomany)->get();
                    $record->$table_manytomany = $data_relation;
                }
            }

            return $record;
        });

        if (! $is_roles) {
            if ($is_field) {
                foreach ($records as $key => $record) {
                    if (
                        isset($record->{$field_identify_related_user}) &&
                        $record->{$field_identify_related_user} != auth()->user()->id
                    ) {
                        unset($records[$key]);
                    }
                }
            }
        }

        $data = [];

        foreach ($records as $row) {
            $data[] = self::getRelationData($data_type, $row);
        }

        $entities['data'] = $data;
        $entities['total'] = count($data);

        return $entities;
    }

    public static function getRelationData($data_type, $row)
    {
        $relational_fields = collect($data_type->dataRows)->filter(function ($value, $key) {
            return $value->relation != null;
        })->all();
        foreach ($relational_fields as $field) {
            $relation_detail = [];

            try {
                $relation_detail = is_string($field->relation) ? json_decode($field->relation) : $field->relation;
                $relation_detail = CaseConvert::snake($relation_detail);
            } catch (\Exception $e) {
            }

            $relation_type = array_key_exists('relation_type', $relation_detail) ? $relation_detail['relation_type'] : null;
            $destination_table = array_key_exists('destination_table', $relation_detail) ? $relation_detail['destination_table'] : null;
            $destination_table_column = array_key_exists('destination_table_column', $relation_detail) ? $relation_detail['destination_table_column'] : null;
            $destination_table_display_column = array_key_exists('destination_table_display_column', $relation_detail) ? $relation_detail['destination_table_display_column'] : null;
            $destination_table_display_more_column = array_key_exists('destination_table_display_more_column', $relation_detail) ? $relation_detail['destination_table_display_more_column'] : null;

            if (
                $relation_type
                && $destination_table
                && $destination_table_column
                && $destination_table_display_column
            ) {
                $arr_query_select = [
                    $destination_table_column,
                    $destination_table_display_column,
                ];

                if (isset($destination_table_display_more_column)) {
                    foreach ($destination_table_display_more_column as $index => $item_destination_table_display_more_column) {
                        if (! in_array($item_destination_table_display_more_column, $arr_query_select)) {
                            $arr_query_select[] = $item_destination_table_display_more_column;
                        }
                    }
                }

                if (isset($row->{$field->field}) && $field->relation['relation_type'] == 'belongs_to_many') {
                    $data_table_destination = DB::table($destination_table)->get();
                    $table_primary_id = $data_type['name'].'_id';
                    $row->{$field->field}->filter(function ($fields, $key) use ($data_table_destination, $destination_table, $destination_table_display_column) {
                        foreach ($data_table_destination as $key => $value) {
                            if ($fields->{$destination_table.'_id'} == $value->id) {
                                $fields->{$destination_table_display_column} = $value->{$destination_table_display_column};
                            }
                        }
                    });
                    $row->{$field->field} = $row->{$field->field}->filter(function ($field, $key) use ($row, $table_primary_id) {
                        if ($field->{$table_primary_id} == $row->id) {
                            return $field;
                        }
                    });
                } else {
                    $relation_datas = DB::table($destination_table)->select($arr_query_select)
                        ->get();
                    switch ($relation_type) {
                        case 'belongs_to':
                            if (isset($row->{$destination_table})) {
                                try {
                                    array_push($row->{$destination_table}, collect($relation_datas)->first());
                                } catch (\Throwable $th) {
                                }
                            } else {
                                $row->{$destination_table} = collect($relation_datas)->toArray();
                            }
                            break;

                        case 'has_many':
                            $row->{$destination_table} = [];
                            foreach ($relation_datas as $key => $relation_data) {
                                if ($relation_data->{$destination_table_column} == $row->id) {
                                    try {
                                        array_push($row->{$destination_table}, $relation_data);
                                    } catch (\Throwable $th) {
                                        $model = DataType::where('slug', $destination_table)->pluck('model_name')->first();
                                        $row->{$destination_table} = $model::where($destination_table_column, $row->id)->get();
                                    }
                                }
                            }
                            break;

                        case 'has_one':
                            $row->{$destination_table} = collect();
                            foreach ($relation_datas as $key => $relation_data) {
                                if ($relation_data->{$destination_table_column} == $row->id) {
                                    $row->{$destination_table} = collect($relation_data);
                                    break;
                                }
                            }
                            break;

                        default:
                            // code...
                            break;
                    }
                }
            }
        }

        return $row;
    }
}