<?php

namespace App\Http\Controllers;

use App\Models\CustomMaterial;
use App\Models\CustomMaterialVariant;
use Illuminate\Http\Request;

class CustomMaterialController extends Controller
{
    public function view()
    {
        $customMaterials = CustomMaterial::all();
        return view('admin.custom-materials.view', compact('customMaterials'));
    }

    public function create()
    {
        return view('admin.custom-materials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'color' => 'nullable|string',
        ]);

        CustomMaterial::create([
            'name'  => $request->name,
            'price' => $request->price,// boleh kosong
        ]);

        return redirect('/admin/custom-materials')->with('success', 'Bahan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $material = CustomMaterial::with('variants')->findOrFail($id);
        return view('admin.custom-materials.edit', compact('material'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'color' => 'nullable|string',
        ]);

        $material = CustomMaterial::findOrFail($id);

        $material->update([
            'name'  => $request->name,
            'price' => $request->price,
            'color' => $request->color,
            'stock' => $request->stock,
        ]);

        return redirect('/admin/custom-materials')->with('success', 'Bahan berhasil diupdate.');
    }

    // --- VARIAN (Warna per Bahan) ---

    public function createVariant($id)
    {
        $material = CustomMaterial::findOrFail($id);
        return view('admin.custom-materials.variants.create', compact('material'));
    }

    public function createVariantAction(Request $request, $id)
    {
        $request->validate([
            'color' => 'required|string',
            'stock' => 'required|integer',
        ]);

        CustomMaterialVariant::create([
            'custom_material_id' => $id,
            'color' => $request->color,
            'stock' => $request->stock,
        ]);

        return redirect()->back()->with('success', 'Warna berhasil ditambahkan.');
    }

    public function editVariant($id)
    {
        $variant = CustomMaterialVariant::findOrFail($id);
        return view('admin.custom-materials.variants.edit', compact('variant'));
    }

    public function updateVariant(Request $request, $id)
    {
        $request->validate([
            'color' => 'required|string',
            'stock' => 'required|integer',
        ]);

        $variant = CustomMaterialVariant::findOrFail($id);

        $variant->update([
            'color' => $request->color,
            'stock' => $request->stock,
        ]);

        return redirect('/admin/custom-materials/detail/' . $variant->custom_material_id)->with('success', 'Varian berhasil diupdate.');
    }

    public function showCustomPage()
    {
        $kebutuhanOptions = [
            'tambak' => 'Kebutuhan tambak/kolam',
            'bertani' => 'Kebutuhan bertani',
            'angkutan' => 'Kebutuhan melindungi angkutan',
            'tenda' => 'Kebutuhan tenda',
            'kebocoran' => 'Kebutuhan kebocoran',
            'bangunan' => 'Kebutuhan bangunan',
            'garam' => 'Kebutuhan melindungi garam',
        ];
    
        $rekomendasiMap = [
            'tambak' => [
                'bahan' => 'A7, A8',
                'deskripsi' => [
                    'A7 lebih ekonomis untuk tambak kecil - Rp 7.000',
                    'A8 lebih tebal dan tahan lama untuk tambak besar - Rp 8.500'
                ]
            ],
            'bertani' => [
                'bahan' => 'A3, A4',
                'deskripsi' => [
                    'A3 cocok untuk penutup lahan ringan - Rp 4.500',
                    'A4 sedikit lebih kuat dan tahan air - Rp 5.000'
                ]
            ],
            'angkutan' => [
                'bahan' => 'Keep Jep, Ulin Orchid',
                'deskripsi' => [
                    'Keep Jep lebih kuat untuk beban berat dan tahan hujan - Rp 70.000',
                    'Ulin Orchid cocok untuk barang kering sementara - Rp 27.000'
                ]
            ],
            'tenda' => [
                'bahan' => 'A4, A5',
                'deskripsi' => [
                    'A4 untuk tenda indoor atau sementara',
                    'A5 lebih kuat untuk outdoor dan cuaca ekstrem'
                ]
            ],
            'kebocoran' => [
                'bahan' => 'A4, A5',
                'deskripsi' => [
                    'A4 cukup untuk tutup bocoran ringan - Rp 5.000',
                    'A5 lebih tahan terhadap tekanan dan tahan sobek - Rp 5.800'
                ]
            ],
            'bangunan' => [
                'bahan' => 'A8, A10',
                'deskripsi' => [
                    'A8 digunakan untuk pelindung proyek ringan - Rp 8.500',
                    'A10 lebih tebal untuk proyek konstruksi berat - Rp 12.000'
                ]
            ],
            'garam' => [
                'bahan' => 'A12, A15',
                'deskripsi' => [
                    'A12 cukup untuk tempat penyimpanan sementara - Rp 10.500',
                    'A15 tahan garam dan sinar UV - Rp 12.000'
                ]
            ],
        ];
    
        $materials = CustomMaterial::with('variants')->get();
    
        return view('custom', compact('kebutuhanOptions', 'materials', 'rekomendasiMap'));
    }

    public function getColors($id)
    {
        $material = CustomMaterial::with('variants')->find($id);
        if (!$material) {
            return response()->json([], 404);
        }

        return response()->json([
            'variants' => $material->variants->map(function ($v) {
                return ['color' => $v->color];
            }),
            'price' => $material->price,
        ]);
    }

    public function customTerpal()
    {
        $kebutuhanOptions = [
            'tambak'     => 'Kebutuhan tambak/kolam',
            'bertani'    => 'Kebutuhan bertani',
            'angkutan'   => 'Kebutuhan melindungi angkutan',
            'tenda'      => 'Kebutuhan tenda',
            'kebocoran'  => 'Kebutuhan kebocoran',
            'bangunan'   => 'Kebutuhan bangunan',
            'garam'      => 'Kebutuhan melindungi garam',
        ];
    
        $rekomendasiMap = [
            'tambak' => [
                'bahan' => 'A7, A8',
                'deskripsi' => [
                    'A7 lebih ekonomis untuk tambak kecil - Rp 7.000',
                    'A8 lebih tebal dan tahan lama untuk tambak besar - Rp 8.500'
                ]
            ],
            'bertani' => [
                'bahan' => 'A3, A4',
                'deskripsi' => [
                    'A3 cocok untuk penutup lahan ringan - Rp 4.500',
                    'A4 sedikit lebih kuat dan tahan air - Rp 5.000'
                ]
            ],
            'angkutan' => [
                'bahan' => 'Keep Jep, Ulin Orchid',
                'deskripsi' => [
                    'Keep Jep lebih kuat untuk beban berat dan tahan hujan - Rp 70.000',
                    'Ulin Orchid cocok untuk barang kering sementara - Rp 27.000'
                ]
            ],
            'tenda' => [
                'bahan' => 'A4, A5',
                'deskripsi' => [
                    'A4 untuk tenda indoor atau sementara',
                    'A5 lebih kuat untuk outdoor dan cuaca ekstrem'
                ]
            ],
            'kebocoran' => [
                'bahan' => 'A4, A5',
                'deskripsi' => [
                    'A4 cukup untuk tutup bocoran ringan - Rp 5.000',
                    'A5 lebih tahan terhadap tekanan dan tahan sobek - Rp 5.800'
                ]
            ],
            'bangunan' => [
                'bahan' => 'A8, A10',
                'deskripsi' => [
                    'A8 digunakan untuk pelindung proyek ringan - Rp 8.500',
                    'A10 lebih tebal untuk proyek konstruksi berat - Rp 12.000'
                ]
            ],
            'garam' => [
                'bahan' => 'A12, A15',
                'deskripsi' => [
                    'A12 cukup untuk tempat penyimpanan sementara - Rp 10.500',
                    'A15 tahan garam dan sinar UV - Rp 12.000'
                ]
            ]
        ];
    
        $materials = CustomMaterial::with('variants')->get();
    
        return view('custom', compact('materials', 'kebutuhanOptions', 'rekomendasiMap'));
    }
    


}
