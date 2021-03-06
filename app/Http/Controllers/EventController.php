<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\Event;
use App\Model\User;
use App\Model\Guard;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * take hte id of an event and an user (User::where('id', $id)) 
     * and return a boolean true if the concerned event is owned by the inline user.
     *
     * @return \Illuminate\Http\Response
     */
    public function verifEventOwner($eventId, $user)
    {
        $userEventsIds = $user->events->map(function($a){
            return $a->id;
        });
        return $userEventsIds->contains($eventId);
    }

    /**
     * take a string générated by autocomplete
     * and return an array with lat and long.
     *
     * @return \Illuminate\Http\Response
     */
    public function geocode($city){
            if ($city) {
                $fullurl = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($city) . "&lang=fr&key=AIzaSyBoZgHPmD27VzTcCSz4UlSm32GqtfYLsuk";
                $string = file_get_contents($fullurl); // get json content
                $geoloc = json_decode($string, true); //json decoder

                $coords = $geoloc['results'][0]['geometry']['location'];
                // $lat = $coords['lat'];
                // $long = $coords['long'];
                return ['lat' => $coords['lat'], 'long' => $coords['lng']];
            }else{
                return ['lat' => '', 'long' => ''];
            }
        }

    /**
     * Show the form for creating a new event.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('event_creation');
    }

    /**
     * Store a newly created event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $event = new Event;

        $event->nom = $request->nom;
        $debut = strtotime($request->debutDate.' '.$request->debutHeure);
        $fin = strtotime($request->finDate.' '.$request->finHeure);

        $geo = $this->geocode($request->place);
        $event->lat = $geo['lat'];
        $event->long = $geo['long'];
        $event->place = $request->place;

        $event->debut = strtotime($request->debutDate.' '.$request->debutHeure);
        $event->fin = strtotime($request->finDate.' '.$request->finHeure);
        $event->place = $request->place;
        $event->billetterie = $request->billetterie;
        $event->textbox = preg_replace("/\r\n|\r|\n/", '<br/>', $request->textbox);
        $event->list_performs = json_encode($request->list_performs);
        // $event->duration = $event->fin - $event->debut;
        // dd(json_encode($request->list_performs));

        if($event->debut < $event->fin){
            $event->save();
            $user = Auth::User();
            $event->users()->sync($user);

            return redirect()->route('event_list_orga');
        }
        return back()->withInput();

    }

    /**
     * Show the form for editing the specified event.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!$this->verifEventOwner($id, Auth::User())){
            return redirect('/event_list_orga');
        }

        $event = Event::all()->where('id', $id)->first();

        return view('event_edition', compact('event'));
    }

    /**
     * Update the specified event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!$this->verifEventOwner($id, Auth::User())){
            return redirect('/event_list_orga');
        }


        $event = Event::all()->where('id', $id)->first();

        $event->nom = $request->nom;
        $debut = strtotime($request->debutDate.' '.$request->debutHeure);
        $fin = strtotime($request->finDate.' '.$request->finHeure);

        $geo = $this->geocode($request->place);
        $event->lat = $geo['lat'];
        $event->long = $geo['long'];
        $event->place = $request->place;

        $event->debut = strtotime($request->debutDate.' '.$request->debutHeure);
        $event->fin = strtotime($request->finDate.' '.$request->finHeure);
        $event->place = $request->place;
        $event->billetterie = $request->billetterie;
        $event->textbox = preg_replace("/\r\n|\r|\n/", '<br/>', $request->textbox);
        $event->list_performs = json_encode($request->list_performs);
        // $event->duration = $event->fin - $event->debut;
        // dd(json_encode($request->list_performs));

        if($event->debut < $event->fin){
            $event->save();
            $user = Auth::User();
            $event->users()->sync($user);

            return redirect()->route('event_list_orga');
        }
        return back()->withInput();

        // $user->events()->sync($event);
        
        return view('event_edition', compact('event'));
    }

    /**
     * Remove the specified event from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!$this->verifEventOwner($id, Auth::User())){
            return redirect('/event_list_orga');
        }

        $event = Event::all()->where('id', $id)->first();

        $event_guards = $event->guards;

        $event_guards->map(function($a){
            // $statut = $a->statut;
            $a->statut = 4;
            $a->events()->detach();
            return $a;
        });

        foreach ($event->guards as $event_guard) {
            $guard = Guard::all()->where('id', $event_guard->id)->first();
            $guard->statut = 4;
            $guard->save();
        }

        // dd($event_guards);
        $event->delete();

        return redirect()->route('event_list_orga');
    }

    /**
     * Display the specified event for user who have orga role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showorga($id)
    {
        $event = Event::All()->where("id", "=", $id)->first();

        return view('event_details_orga', compact('event'));
    }

    /**
     * Display the specified event for user who have proguard role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showproguard($id)
    {
        $event = Event::All()->where("id", "=", $id)->first();
        if($event){
            return view('event_details_proguard', compact('event'));
        }
        redirect('event_details_proguard');
    }

    /**
     * Display the specified event for user who have procult role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showprocult($id)
    {
        $event = Event::All()->where("id", "=", $id)->first();
        // dd($event);
        if($event !== null){
        
            $guards = $event->guards->map(function($a){
                $decoded_list_places = json_decode($a->list_places);
                $a->list_places = $decoded_list_places;
                return $a;
            });
            return view('event_details_procult', compact('event', 'guards'));
        
        }
        redirect('/afet');
    }

    /**
     * Display the specified user's events.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userall(Request $request)
    {
        $events = $request->user()->events;

        return view('event_list_orga', compact('events'));
    }

    /**
     * Display all events and the number of guard on each of them.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $events = Event::all();
        $guards_nb = Event::all()->map(function($event){
            return count($event->guards);
        });

        // dd($guards_nb);
        return view('event_search', compact('events', 'guards_nb'));
    }

    /**
     * Show the form used by proguard for create a guard on an event.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function SubProGuardDetails(Request $request, $id)
    {
        // dump($request);
        $event = Event::All()->where("id", "=", $id)->first();

        return view('sub_proguard_details', compact('event'));
    }

    /**
     * Store a newly created guard in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function SubProGuard(Request $request, $id)
    {
        $guard = new Guard;
        $guard->list_places = json_encode($request->list_places);
        $guard->list_child_nbs = json_encode($request->list_child_nbs);
        $guard->list_range = json_encode($request->list_range); // rayon autour du quel le pro peut garder.

        $guard->debut = strtotime($request->debutDate.' '.$request->debutHeure);
        $guard->fin = strtotime($request->finDate.' '.$request->finHeure);
        $guard->textbox = preg_replace("/\r\n|\r|\n/", '<br/>', $request->textbox);

        $user = Auth::User();
        $event = Event::All()->where("id", "=", $id)->first();

        return redirect()->route('event_list_proguard');
        // return redirect()->route('event_search');
    }
}
