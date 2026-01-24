<?php

use App\Models\Quebra;
use Livewire\Volt\Component;

new class extends Component {
    public function getTotalHojeProperty()
    {
        return Quebra::whereDate('created_at', today())->count();
    }

    public function getPendentesProperty()
    {
        return Quebra::where('status', 'pendente')->count();
    }
};
?>

<div>
    <h1 class="text-2xl font-bold mb-6">
        Dashboard
    </h1>

    <div class="grid grid-cols-3 gap-4">

        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">Quebras Hoje</div>
            <div class="text-2xl font-bold">{{ $this->totalHoje }}</div>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">Pendentes</div>
            <div class="text-2xl font-bold">{{ $this->pendentes }}</div>
        </div>

    </div>
</div>
