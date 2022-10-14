<?php

namespace App\Http\Livewire\Pages;

use App\Actions\FinanceAmount;
use App\Models\Finance;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    private LengthAwarePaginator $finances;
    private string $monthExpenses = '';
    private string $monthIncomings = '';
    private string $monthTotal = '';

    protected $listeners = ['refreshFinances' => '$refresh'];

    public function mount()
    {
        $this->finances = Finance::query()
            ->where('user_id', Auth::user()->id)
            ->whereMonth('date', now()->month)
            ->orderBy('created_at', 'DESC')
            ->paginate(6);

        $this->monthExpenses = (new FinanceAmount)->run(
            now()->month,
            now()->year,
            'Expense'
        );

        $this->monthIncomings = (new FinanceAmount)->run(
            now()->month,
            now()->year,
            'Incoming'
        );

        $this->monthTotal = ($this->monthIncomings - $this->monthExpenses);
    }
    public function render()
    {
        return view('livewire.pages.dashboard', [
            'finances' => $this->finances,
            'monthExpenses' => number_format($this->monthExpenses, 2, ','),
            'monthIncomings' => number_format($this->monthIncomings, 2, ','),
            'monthTotal' => number_format($this->monthTotal, 2, ','),
        ]);
    }
}
