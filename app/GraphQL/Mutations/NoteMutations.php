<?php

namespace App\GraphQL\Mutations;

use App\Models\Note;
use App\Models\Agenda_Tecnico;

class NoteMutations
{
    public function create($root,array $args){
        $noteData = $args['requestNote'];
        $note = new Note();
        $note->descripcion = $noteData['descripcion'];
        $agenda = Agenda_Tecnico::find($noteData['agenda_id']);
        if(!$agenda){
            throw new \Exception('Agenda no encontrada');
        }
        $note->agenda_tecnico_id = $agenda->id;
        $note->save();
        return $note;
    }

    public function update($root , array $args){
        $note_id = $args['id'];
        $note = Note::find($note_id);
        $note->descripcion = $args['descripcion'];
        $note->save();
    }
}
