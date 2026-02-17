<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    protected $table = 'warga';
    
    protected $fillable = [
        'nik',
        'no_kk',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'religious',
        'education',
        'living_status',
        'married_status',
        'occupation',
        'blood_type'
    ];
    
    protected $casts = [
        'birth_date' => 'date',
    ];
    
    
    public function family()
    {
        return $this->belongsTo(Family::class, 'no_kk', 'no_kk');
    }
    
    
    
    public function isHead()
    {
        if (!$this->no_kk) {
            return false;
        }
        
        $family = $this->family;
        return $family && $family->family_head_id === $this->id;
    }
    
    
    public function assignToFamily($noKk)
    {
        $family = Family::where('no_kk', $noKk)->first();
        
        if (!$family) {
            throw new \Exception('Keluarga tidak ditemukan');
        }
        
        
        if ($this->no_kk && $this->no_kk !== $noKk) {
            
            if ($this->isHead()) {
                $this->family->update(['family_head_id' => null]);
            }
        }
        
        $this->update(['no_kk' => $noKk]);
        
        return $this->fresh();
    }
    
    
    
    
    public function removeFromFamily()
    {
        if (!$this->no_kk) {
            throw new \Exception('Warga tidak terdaftar di keluarga manapun');
        }
        
        
        if ($this->isHead()) {
            $this->family->update(['family_head_id' => null]);
        }
        
        $this->update(['no_kk' => null]);
        
        return $this->fresh();
    }
    
    
    
    public function moveToFamily($noKk)
    {
        return $this->assignToFamily($noKk);
    }
    
    
    
    
    public function scopeWithoutFamily($query)
    {
        return $query->whereNull('no_kk');
    }
    
    
    
    
    public function scopeWithFamily($query)
    {
        return $query->whereNotNull('no_kk');
    }
    
    
    
    public function scopeAlive($query)
    {
        return $query->where('living_status', 'hidup');
    }
}