<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Event; // acessando o model que da acesso ao banco de dados
use App\Models\User;

class EventController extends Controller
{
    public function index() {

        $search = request('search'); // variavel que vem do front (barra de pesquisa)

        if ($search) { // se search existir:
            
            $events = Event::where([ // variavel que vai conter todo o banco de dados
                ['title', 'like', '%'.$search.'%'] // caso exista o $search e procurado (where) no banco com a logica
            ])->get();

        } else { // se search nao existir:
            $events = Event::all(); // caso nao tenha o $search e retornado todos (all) os eventos
        }

        return view('welcome',['events' => $events, 'search' => $search]); /* retorno de uma view junto
                                                                           com o envio de variaveis para a propria view */
    }


    public function create() {
        return view('events.create'); // retornando apenas uma view
    
    }


    public function store(Request $request) {
        
        $event = new Event; 

        $event->title = $request->title; // atribuindo valores
        $event->date = $request->date; // atribuindo valores
        $event->city = $request->city; // atribuindo valores
        $event->private = $request->private; // atribuindo valores
        $event->description = $request->description; // atribuindo valores
        $event->items = $request->items; // atribuindo valores

        if($request->hasFile('image') && $request->file('image')->isValid()) { /* verificando se existe o arquivo image e se ele
                                                                                  e valido */
            $requestImage = $request->image; // atribuindo o valor vindo do front (imagem) a uma variavel

            $extension = $requestImage->extension(); // capturando a extensao do arquivo

            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension; /* atribuindo a uma
                                                                     variavel o nome do arquivo + o momento do upload + a extensao */
            $requestImage->move(public_path('img/events'), $imageName); // salvando a imagem na pasta 'img/events e dando um nome a ela

            $event->image = $imageName; // atribuindo valor 
        }

        $user =auth()->user();
        $event->user_id = $user->id;

        $event->save();

        return redirect('/')->with('msg', 'Evento criado com sucesso!'); /* redirecionando apos a gravacao dos dados e enviando uma 
                                                                            flash message para o main.blade.php */
    }

    public function show($id) { // recebendo o id do evento do front

        $event = Event::findOrFail($id); // procurando o evento (com base no id) no banco

        $user = auth()->user(); // acesso ao usuario logado

        $hasUserJoined = false; // usuario nao participa do evento

        if($user) { // se o usuario esta autenticado (se ele acessou a rota com o login feito) 

                $userEvents = $user->eventsAsParticipant->toArray(); /* variavel = usuario autenticado->
                funcao do model User->transformando em array*/ // Eventos que o usuario participa

                foreach ($userEvents as $userEvent) {
                    if ($userEvent['id'] == $id) {
                        $hasUserJoined = true;
                    }
                }
        }
      

        $eventOwner = User::where('id', $event->user_id)->first()->toArray(); /* procurando se o usuario autentucado e o dono do evento,
        where tem o segundo parametro oculto (seria , '=' , ), primeiro que encontrar e transformando o dado que e um objeto em array*/

        return view('events.show', ['event' => $event, 'eventOwner' => $eventOwner, 'hasUserJoined' => $hasUserJoined]);
        // retornando view junto com o envio de variaveis para a propria view
    }

    public function dashboard() {

        $user = auth()->user();

        $events = $user->events;

        $eventsAsParticipant = $user->eventsAsParticipant;

        return view('events.dashboard',
             ['events' => $events, 'eventsAsParticipant' => $eventsAsParticipant]);
    }

    public function destroy($id) {

        /*$role = Event::where('id','+', $id)->first(); 
        $role->detach(); 
        $role->delete();
        */
        
        Event::findOrFail($id)->delete();

        return redirect('/dashboard')->with('msg', "Evento excluido com sucesso!");

    }

    public function edit($id) {

        $user = auth()->user();

        $event = Event::findOrFail($id);

        if($user->id != $event->user_id) {
            return redirect('/dashboard');
        }

        return view('events.edit', ['event' => $event]);
    }

    public function update(Request $request) {

        $data = $request->all();

        if($request->hasFile('image') && $request->file('image')->isValid()) {
            
            $requestImage = $request->image;

            $extension = $requestImage->extension();

            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;

            $request->image->move(public_path('img/events'), $imageName);

            $data['image'] = $imageName;
        }

        Event::findOrFail($request->id)->update($data);

        return redirect('/dashboard')->with('msg', "Evento editado com sucesso!");

    }

    public function joinEvent($id) {
        
        $user = auth()->user();

        $user->eventsAsParticipant()->attach($id);

        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Sua presenca esta confirmada no evento ' . $event->title);

    }

    public function leaveEvent($id) {

        $user = auth()->user();

        $user->eventsAsParticipant()->detach($id);

        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Sua presenca foi removida do evento ' . $event->title);

    }
}
