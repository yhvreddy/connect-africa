<?php

namespace App\Http\Controllers;
use App\Repositories\EntertainmentMasterRepository;
use App\Models\EntertainmentMasterData;
use Illuminate\Http\Request;
use App\Http\Traits\TruFlix;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Http\Requests\GenresRequest;
use App\Http\Requests\GenresUpdateRequest;

class GenresController extends Controller
{

    use TruFlix, HttpResponses;

    protected $emdRepository;

    public function __construct(
        EntertainmentMasterRepository $_entertainmentMasterDataRepository,
    ){
        $this->emdRepository = $_entertainmentMasterDataRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $genres = $this->emdRepository->where('type', 'genres')->get();
        return view('zq.master-data.genres.list', compact('genres'));
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $genres = $this->emdRepository->where('type', 'genres')->get();

        return view('zq.master-data.genres.add', compact('genres'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GenresRequest $request)
    {
        try {
    
            $response = DB::transaction(function () use ($request) {
                $data = $request->all();

                $slug = $this->generateSlug($data['slug']);
                $checkSlug = $this->emdRepository->where('slug', $slug)->first();
                if($checkSlug){
                    return $this->validation('Sorry, Slug is already exists.');
                }

                //Map Data to save details
                $genreData = [
                    'title' => $data['title'],
                    'slug' => $slug,
                    'type' => $data['type'],
                ];

               
                $genre = $this->emdRepository->create($genreData);
                if($genre){
                    
                    $genre->save();

                    return $this->objectCreated('Genre details saved successfully.', $genre);
                }

                return $this->validation('Sorry, Genre details failed to save, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.genres.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntertainmentMasterData $genre)
    {
        return view('zq.master-data.genres.edit', compact('genre'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GenresUpdateRequest $request, EntertainmentMasterData $genre)
    {
        try {
            $response = DB::transaction(function () use ($request, $genre) {

                $data = $request->all();

                $slug = $this->generateSlug($data['slug']);
                $checkSlug = $this->emdRepository->whereNot('id', $genre->id)->where('slug', $slug)->first();
                if($checkSlug){
                    return $this->validation('Sorry, Slug is already exists.');
                }

                //Map Data to save details
                $genre->title           =   $data['title'];
                $genre->slug            =   $slug;
                
                if($genre->save()){
                    return $this->objectCreated('Genres details updated successfully.', $genre);
                }

                return $this->validation('Sorry, Genres details failed to update, please try again.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.genres.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            // Log exception
            Log::error('Failed to update genre: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Failed to update genre: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function softDelete(EntertainmentMasterData $genre)
    {
        try {
            $response = DB::transaction(function () use ($genre) {
                if ($genre->delete()) {
                    return $this->success('Genre deleted successfully.');
                }

                return $this->validation('Invalid Request to delete Genre.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->route('zq.genres.index')->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);
            
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function genreFetchDataListForAjax(Request $request){
        //Defined Columns
        $columnArray = [
            'id',
            'title',
            'id',
        ];

        $data = $request->all();
        $limit = $request->input('length');
        $start = $request->input('start');
        
        $order = $columnArray[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        $genres = $this->emdRepository->where('type','genres');


        $totalData = count($genres->get());
        $totalFiltered = $totalData; 

        //Filter Data
        if(!empty($data['search']) || !empty($data['duration']) || !empty($data['status'])){ //.... conduction's can add in this if clause

            if(!empty($data['search'])){
                $genres->where('type', 'Like', '%'.$data['search'].'%')
                           ->orWhere('title', 'Like', '%'.$data['search'].'%');
            }

        // Filter by user status (active or deactivated)
        if (!empty($data['status'])) {
            switch ($data['status']) {
                case 'active':
                    $genres->where('is_active', 1);
                    break;
                case 'deactivated':
                    $genres->where('is_active', 0);
                    break;
            }
        }


            $totalFiltered = count($genres->get());
        }

        $genres = $genres
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
        
        //Customize or add additional data in below loop
        $newGenres = [];
        foreach($genres as $key => $genre){
            $activateButton = $genre->is_active ? 'Deactivate' : 'Activate';
            $activateAction = $genre->is_active ? 'deactivate' : 'activate';
            $actions = '<ul class="action align-center">
                            <li class="view"> 
                                <a href="javascript;0;"><i class="icon-eye"></i></a>
                            </li>
                            <li class="edit"> 
                                <a href="'.route('zq.genres.edit', ['genre' => $genre->id]).'">
                                    <i class="icon-pencil-alt"></i></a>
                            </li>
                            <li class="delete"><a href="'.route('zq.genres.soft-delete', ['genre' => $genre->id]).'" onclick=" return confirm(\'Are you sure to delete this genre?\');"><i class="icon-trash"></i></a></li>
                            <li class="activate">
                                <a href="javascript:void(0);" class="btn-activate" data-id="'.$genre->id.'" data-action="'.$activateAction.'">
                                    '.$activateButton.'
                                </a>
                            </li>
                        </ul>';

            $newGenre = [
                'sno' => $key +1,
                'title' => $genre->title,
                'actions' => $actions
            ];
            array_push($newGenres, $newGenre);
        }

        $response['draw'] = intval($data['draw']);
        $response['recordsTotal'] = intval($totalData);
        $response['recordsFiltered'] = intval($totalFiltered);
        $response['data'] = collect($newGenres);
        return response()->json($response, 200);
    }

    public function updateStatus(Request $request, $genreId, $action)
    {
        $genre = EntertainmentMasterData::findOrFail($genreId);

        if (!$genre) {
            return response()->json(['error' => 'genre not found.'], 404);
        }

        if ($action === 'activate') {
            $genre->is_active = 1;
        } elseif ($action === 'deactivate') {
            $genre->is_active = 0;
        } else {
            return response()->json(['error' => 'Invalid action.'], 400);
        }

        $genre->save();

        return response()->json(['message' => 'genre status updated successfully.'], 200);
    }
}
