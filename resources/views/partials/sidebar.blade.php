@role('admin')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>

<p class="mt-4 font-semibold">Surat Menyurat</p>
<a href="{{ route('admin.surat.masuk') }}">Surat Masuk</a>
<a href="{{ route('admin.surat.approved') }}">Surat Disetujui</a>
<a href="{{ route('admin.surat.rejected') }}">Surat Ditolak</a>
<a href="{{ route('admin.surat.proses-ttd') }}">Proses TTD</a>
<a href="{{ route('admin.surat.selesai') }}">Surat Selesai</a>

<p class="mt-4 font-semibold">Master</p>
<a href="{{ route('admin.master.users.index') }}">User</a>
<a href="{{ route('admin.master.roles.index') }}">Role</a>
<a href="{{ route('admin.master.jenis-surat.index') }}">Jenis Surat</a>
<a href="{{ route('admin.master.pengaturan') }}">Pengaturan</a>
@endrole

@role('sekretaris')
<a href="{{ route('sekretaris.approval.index') }}">Dashboard</a>

<p class="mt-4 font-semibold">Surat Menyurat</p>
<a href="{{ route('sekretaris.surat.masuk') }}">Surat Masuk</a>
<a href="{{ route('sekretaris.surat.approved') }}">Surat Disetujui</a>
<a href="{{ route('sekretaris.surat.rejected') }}">Surat Ditolak</a>
<a href="{{ route('sekretaris.surat.proses-ttd') }}">Proses TTD</a>
<a href="{{ route('sekretaris.surat.selesai') }}">Surat Selesai</a>
@endrole
