<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;
    #[Rule('required|min:3|max:50')]
    public $name;
    public $search;
    public $editingTodoID;

    // #[Rule('required|min:3|max:50')]
    public $editingTodoName;
    public function create()
    {
        $vadilated = $this->validate();
        Todo::create($vadilated);
        $this->reset('name');
        session()->flash('success', 'Todo created successfully');
    }
    public function delete(Todo $todo)
    {
        $todo->delete();
    }
    public function toggle($toggleID)
    {
        $todo = Todo::find($toggleID);
        $todo->completed = !$todo->completed;
        $todo->save();
    }
    public function edit($todoID)
    {
        $this->editingTodoID = $todoID;
        $this->editingTodoName = Todo::find($todoID)->name;
    }
    public function update()
    {
        $this->validateOnly('editingTodoName');
        Todo::find($this->editingTodoID)->update(
            [
                'name' => $this->editingTodoName

            ]
        );
        $this->cancelEdit();
    }
    public function cancelEdit()
    {
        $this->reset('editingTodoID', 'editingTodoName');
    }
    public function render()
    {
        return view('livewire.todo-list', [
            'todos' => Todo::latest()->where('name', 'LIKE', '%' . $this->search . '%')->paginate(3)
        ]);
    }
}
