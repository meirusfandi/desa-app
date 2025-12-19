<aside class="w-64 bg-white shadow">
    <div class="p-4 font-bold">E-Desa</div>

    <ul class="p-4 space-y-2">

        @role('warga')
        <li><a href="/warga/surat">Ajukan Surat</a></li>
        @endrole

        @role('admin')
        <li><a href="/admin/surat-types">Jenis Surat</a></li>
        @endrole

        @role('sekretaris')
        <li>
            <a href="{{ route('sekretaris.approval.index') }}">
                Approval Surat
            </a>
        </li>
        @endrole

        @role('kepala_desa')
        <li><a href="/kepala-desa/sign">Tanda Tangan</a></li>
        @endrole

        <li class="mt-4">
            <form method="POST" action="/logout">
                @csrf
                <button>Logout</button>
            </form>
        </li>
    </ul>
</aside>
