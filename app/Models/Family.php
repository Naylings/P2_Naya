<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Family extends Model
{   
    protected $table = 'family';
    
    protected $fillable = [
        'no_kk',
        'rt_id',
        'rw_id',
        'address',
        'family_head_id'
    ];
    
    
    public function members()
    {
        return $this->hasMany(Warga::class, 'no_kk', 'no_kk');
    }
    
    
    public function familyHead()
    {
        return $this->belongsTo(Warga::class, 'family_head_id');
    }
    
    public function rt()
    {
        return $this->belongsTo(Rukun::class, 'rt_id');
    }
    
    public function rw()
    {
        return $this->belongsTo(Rukun::class, 'rw_id');
    }
    
    
    
    
    public function assignMembers(array $wargaIds)
    {
        if (empty($wargaIds)) {
            return 0;
        }
        
        return Warga::whereIn('id', $wargaIds)
            ->update(['no_kk' => $this->no_kk]);
    }
    
    
    public function setHead($wargaId)
    {
        $warga = Warga::find($wargaId);
        
        if (!$warga) {
            throw new \Exception('Warga tidak ditemukan');
        }
        
        
        if ($warga->no_kk !== $this->no_kk) {
            throw new \Exception('Warga bukan anggota keluarga ini');
        }
        
        $this->update(['family_head_id' => $wargaId]);
        
        return $this->fresh();
    }
    
    
    public function removeMember($wargaId)
    {
        $warga = Warga::find($wargaId);
        
        if (!$warga || $warga->no_kk !== $this->no_kk) {
            throw new \Exception('Warga bukan anggota keluarga ini');
        }
        
        
        if ($this->family_head_id == $wargaId) {
            $this->update(['family_head_id' => null]);
        }
        
        $warga->update(['no_kk' => null]);
        
        return true;
    }
    
    
    public function removeAllMembers()
    {
        DB::transaction(function () {
            // Set family_head_id null dulu
            $this->update(['family_head_id' => null]);
            
            // Set semua no_kk anggota jadi null
            Warga::where('no_kk', $this->no_kk)
                ->update(['no_kk' => null]);
        });
        
        return true;
    }
    
    
    public function getAvailableHeads()
    {
        return Warga::where('no_kk', $this->no_kk)->get();
    }

    
    
    public function scopeWithMembersCount($query)
    {
        return $query->withCount('members');
    }
}