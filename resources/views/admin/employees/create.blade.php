@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow p-4 sm:p-6 lg:p-8 px-4 sm:px-0">
    <h1 class="text-xl sm:text-2xl font-bold mb-1 sm:mb-2 text-gray-800">Tambah Pegawai</h1>
    <p class="text-sm sm:text-base text-gray-500 mb-4 sm:mb-6">Isi data pegawai dengan lengkap dan benar.</p>
    <form method="POST" class="space-y-4 sm:space-y-5">
        @csrf
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" name="name" placeholder="Nama Pegawai" class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none" required>
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" placeholder="Email" class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none" required>
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">No Telp</label>
            <input type="text" name="phone" placeholder="No Telp" class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">No KTP</label>
            <input type="text" name="ktp" placeholder="No KTP" class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Plat Mobil (Khusus Driver)</label>
                <input type="text" name="car_plate" id="car_plate" placeholder="Plat Mobil" class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none" disabled>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Tipe Mobil (Khusus Driver)</label>
                <input type="text" name="car_type" id="car_type" placeholder="Tipe Mobil" class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none" disabled>
            </div>
        </div>
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" placeholder="Password" class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none" required>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" id="role" class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none" required>
                    <option value="admin">Admin</option>
                    <option value="gudang">Gudang</option>
                    <option value="keuangan">Keuangan</option>
                    <option value="owner">Owner</option>
                    <option value="driver">Driver</option>
                </select>
            </div>
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="active" class="w-full border border-gray-300 rounded-md px-3 sm:px-4 py-2 text-sm focus:ring-2 focus:ring-teal-300 outline-none" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
        <button class="w-full bg-teal-600 hover:bg-teal-700 text-white text-sm sm:text-base font-bold py-2.5 sm:py-3 rounded-lg transition">Submit</button>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const carPlate = document.getElementById('car_plate');
    const carType = document.getElementById('car_type');
    function toggleDriverFields() {
        const isDriver = roleSelect.value === 'driver';
        carPlate.disabled = !isDriver;
        carType.disabled = !isDriver;
        if (!isDriver) {
            carPlate.value = '';
            carType.value = '';
        }
    }
    roleSelect.addEventListener('change', toggleDriverFields);
    toggleDriverFields(); // initial check
});
</script>
@endsection
