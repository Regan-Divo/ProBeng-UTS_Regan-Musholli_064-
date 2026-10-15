' ===================================================================
'  GENERATOR DIAGRAM PROBENG  (Visio COM automation via VBScript)
'  Menghasilkan 3 file .vsdx + 3 .png :
'    1. Use Case Diagram - ProBeng
'    2. Class Diagram - ProBeng
'    3. Entity Relationship Diagram - ProBeng
' ===================================================================
Option Explicit

Const STENCIL = "C:\Program Files\Microsoft Office\root\Office16\Visio Content\1033\BASIC_M.VSSX"
Const OUTDIR  = "C:\Users\regan\OneDrive\STATISTIKA\RPL TUGAS\"

' ---------- palet warna (samakan dengan contoh Perpustakaan) ----------
Dim C_TITLE, C_UC, C_UCLINE, C_INC, C_INCLINE, C_BOUND, C_ACTOR
Dim C_CLASSTXT, C_ERDTXT, C_ERDROW
C_TITLE   = "RGB(31,56,100)"
C_UC      = "RGB(255,242,204)"
C_UCLINE  = "RGB(191,144,0)"
C_INC     = "RGB(218,242,208)"
C_INCLINE = "RGB(56,118,29)"
C_BOUND   = "RGB(128,128,128)"
C_ACTOR   = "RGB(31,78,121)"
C_CLASSTXT= "RGB(31,78,121)"
C_ERDTXT  = "RGB(31,78,121)"
C_ERDROW  = "RGB(242,246,250)"

Dim g_visio, g_fso

' ===================================================================
'  SUB UTAMA
' ===================================================================
Sub Main()
    Set g_visio = CreateObject("Visio.Application")
    g_visio.Visible = False
    g_visio.AlertResponse = 7
    Set g_fso = CreateObject("Scripting.FileSystemObject")

    BangunUseCase
    BangunClassDiagram
    BangunERD

    g_visio.Quit
    WScript.Echo "SEMUA DIAGRAM SELESAI DIBUAT."
End Sub

' ===================================================================
'  1. USE CASE DIAGRAM
' ===================================================================
Sub BangunUseCase()
    Dim doc, page, st, shp, i

    Set doc  = g_visio.Documents.Add("")
    Set page = doc.Pages.Item(1)
    Set st   = g_visio.Documents.Open(STENCIL)
    PageSetup page, "15.5 in", "10 in"

    ' ---- judul ----
    AddText page, st, 7.75, 9.72, "Use Case Diagram - Sistem Manajemen Servis & Suku Cadang Bengkel", 18, True, C_TITLE

    ' ---- batas sistem ----
    Set shp = AddRect(page, st, 1.55, 0.45, 14.1, 9.35, "")
    shp.CellsU("FillPattern").FormulaU = "0"
    shp.CellsU("LinePattern").FormulaU = "2"
    shp.CellsU("LineColor").FormulaU = C_BOUND
    shp.CellsU("LineWeight").FormulaU = "1.5 pt"

    AddText page, st, 7.8, 9.12, "Sistem Manajemen Servis & Suku Cadang Bengkel", 16, True, C_TITLE

    ' ---- aktor kiri : Mekanik ----
    AddActor page, st, 0.75, 4.6, "Mekanik"
    ' ---- aktor kanan : Pemilik Bengkel ----
    AddActor page, st, 14.9, 4.6, "Pemilik Bengkel"

    ' ---- use case kolom kiri ----
    Dim lx
    lx = 4.2
    Dim UCkiri, ykiri
    UCkiri = Array("Tambah Pelanggan", "Ubah Pelanggan", "Hapus Pelanggan", "Cari Pelanggan", _
                   "Tambah Suku Cadang", "Ubah Suku Cadang", "Hapus Suku Cadang", "Tambah Stok")
    ykiri = Array(8.25, 7.25, 6.25, 5.25, 4.25, 3.25, 2.25, 1.25)
    Dim shpKiri(7)
    For i = 0 To 7
        Set shpKiri(i) = AddEllipse(page, st, lx, ykiri(i), 3.1, 0.78, UCkiri(i), C_UC, C_UCLINE, 11)
    Next

    ' ---- use case kolom kanan ----
    Dim rx
    rx = 11.7
    Dim UCkanan, ykanan
    UCkanan = Array("Lihat Dashboard", "Buat Transaksi Servis", "Tambah Rincian Barang", "Ubah Status Servis", _
                    "Hapus Transaksi Servis", "Lihat Riwayat Servis", "Lihat Stok Menipis", "Cetak Nota Pembayaran")
    ykanan = Array(8.25, 7.25, 6.25, 5.25, 4.25, 3.25, 2.25, 1.25)
    Dim shpKanan(7)
    For i = 0 To 7
        Set shpKanan(i) = AddEllipse(page, st, rx, ykanan(i), 3.1, 0.78, UCkanan(i), C_UC, C_UCLINE, 11)
    Next

    ' ---- use case tengah bawah : Login ----
    Dim shpLogin
    Set shpLogin = AddEllipse(page, st, 7.95, 0.95, 2.4, 0.72, "Login", C_UC, C_UCLINE, 11)

    ' ---- use case hijau (include) ----
    Dim shpInc(2)
    Set shpInc(0) = AddEllipse(page, st, 7.95, 6.75, 3.0, 0.75, "Validasi Data Pelanggan", C_INC, C_INCLINE, 11)
    Set shpInc(1) = AddEllipse(page, st, 7.95, 4.6, 3.0, 0.75, "Validasi Stok Suku Cadang", C_INC, C_INCLINE, 11)
    Set shpInc(2) = AddEllipse(page, st, 7.95, 2.35, 3.0, 0.75, "Hitung Total Biaya", C_INC, C_INCLINE, 11)

    ' ---- relasi include ----
    AddArrow page, shpInc(0), shpKiri(0), "<<include>>", C_INCLINE
    AddArrow page, shpInc(0), shpKiri(1), "<<include>>", C_INCLINE
    AddArrow page, shpInc(1), shpKiri(4), "<<include>>", C_INCLINE
    AddArrow page, shpInc(1), shpKanan(2), "<<include>>", C_INCLINE
    AddArrow page, shpInc(1), shpKiri(7), "<<include>>", C_INCLINE
    AddArrow page, shpInc(2), shpKanan(1), "<<include>>", C_INCLINE
    AddArrow page, shpInc(2), shpKanan(7), "<<include>>", C_INCLINE

    ' ---- relasi aktor - use case ----
    Dim i2
    For i2 = 0 To 7
        AddPlainLine page, 1.3, 4.6, lx - 1.55, ykiri(i2), "RGB(64,64,64)"
    Next
    ' aktor kiri ke use case kanan (servis)
    AddPlainLine page, 1.3, 4.6, rx - 1.55, ykanan(1), "RGB(64,64,64)"
    AddPlainLine page, 1.3, 4.6, rx - 1.55, ykanan(3), "RGB(64,64,64)"
    AddPlainLine page, 1.3, 4.6, rx - 1.55, ykanan(4), "RGB(64,64,64)"

    ' aktor kanan
    Dim titikKanan
    titikKanan = Array(-3.65, -1.25, -2.45, -6.05, -8.45)
    AddPlainLine page, 14.4, 4.6, rx + 1.55, ykanan(0), "RGB(64,64,64)"
    AddPlainLine page, 14.4, 4.6, rx + 1.55, ykanan(5), "RGB(64,64,64)"
    AddPlainLine page, 14.4, 4.6, rx + 1.55, ykanan(6), "RGB(64,64,64)"
    AddPlainLine page, 14.4, 4.6, rx + 1.55, ykanan(7), "RGB(64,64,64)"
    AddPlainLine page, 14.4, 4.6, shpLogin.CellsU("PinX").ResultIU, shpLogin.CellsU("PinY").ResultIU, "RGB(64,64,64)"
    AddPlainLine page, 1.3, 4.6, shpLogin.CellsU("PinX").ResultIU, shpLogin.CellsU("PinY").ResultIU, "RGB(64,64,64)"

    ' ---- legenda ----
    AddText page, st, 7.75, 0.12, "Legenda: <<include>> = perilaku yang wajib dipanggil oleh use case utama.", 9, False, "RGB(89,89,89)"

    Simpan doc, page, "01 Use Case Diagram - ProBeng"
    st.Close
    doc.Close
End Sub

' ===================================================================
'  2. CLASS DIAGRAM
' ===================================================================
Sub BangunClassDiagram()
    Dim doc, page, st, i
    Set doc  = g_visio.Documents.Add("")
    Set page = doc.Pages.Item(1)
    Set st   = g_visio.Documents.Open(STENCIL)
    PageSetup page, "15.5 in", "11 in"

    AddText page, st, 7.75, 10.6, "Class Diagram - Sistem Manajemen Servis & Suku Cadang Bengkel", 18, True, C_TITLE

    Dim cPel, cSv, cDet, cSp
    Set cPel = AddClassBox(page, st, 0.6, 1.3, 3.9, "Pelanggan", _
        Array("- id_pelanggan : int", "- nama_pelanggan : varchar(100)", "- no_telepon : varchar(20)", _
              "- plat_nomor : varchar(20)", "- jenis_kendaraan : varchar(50)"), _
        Array("+ getAll()", "+ getById(id)", "+ savePelanggan()", "+ updatePelanggan()", _
              "+ deletePelanggan()", "+ cekPlatNomor()"), 9)

    Set cSv = AddClassBox(page, st, 5.9, 1.3, 9.2, "Servis", _
        Array("- id_servis : int", "- id_pelanggan : int (FK)", "- keluhan : text", _
              "- biaya_jasa : int", "- status_servis : enum", "- tanggal_masuk : datetime"), _
        Array("+ getAll()", "+ getById(id)", "+ getDetailServis()", "+ simpanTransaksi()", _
              "+ updateStatus()", "+ deleteServis()", "+ getTotalPendapatan()"), 9)

    Set cDet = AddClassBox(page, st, 11.2, 1.3, 14.4, "DetailServis", _
        Array("- id_detail : int", "- id_servis : int (FK)", "- id_sparepart : int (FK)", _
              "- jumlah : int", "- subtotal : int"), _
        Array("+ getByServis()", "+ hapusDetail()"), 9)

    Set cSp = AddClassBox(page, st, 11.2, 6.0, 14.4, "Sparepart", _
        Array("- id_sparepart : int", "- nama_barang : varchar(100)", "- harga_beli : int", _
              "- harga_jual : int", "- stok : int"), _
        Array("+ getAll()", "+ getById(id)", "+ saveSparepart()", "+ updateSparepart()", _
              "+ stokBertambah()", "+ stokBerkurang()", "+ getStokMenipis()"), 9)

    AddRelasi page, 3.9, 3.0, 5.9, 3.0, "1", "0..*", "mempunyai", 0, 0
    AddRelasi page, 9.2, 3.0, 11.2, 3.0, "1", "1..*", "terdiri dari", 0, 0
    AddRelasi page, 12.8, 6.0, 12.8, 3.84, "1", "0..*", "dipakai pada", -0.5, -0.85

    AddText page, st, 7.75, 0.45, _
        "Relasi: Pelanggan 1 --- 0..* Servis  |  Servis 1 --- 1..* DetailServis  |  Sparepart 1 --- 0..* DetailServis", _
        10, False, "RGB(89,89,89)"

    Simpan doc, page, "02 Class Diagram - ProBeng"
    st.Close
    doc.Close
End Sub

' ===================================================================
'  3. ENTITY RELATIONSHIP DIAGRAM
' ===================================================================
Sub BangunERD()
    Dim doc, page, st, i
    Set doc  = g_visio.Documents.Add("")
    Set page = doc.Pages.Item(1)
    Set st   = g_visio.Documents.Open(STENCIL)
    PageSetup page, "16 in", "11 in"

    AddText page, st, 8.0, 10.6, "Entity Relationship Diagram - Sistem Manajemen Servis & Suku Cadang Bengkel", 18, True, C_TITLE

    Dim tPel, tSp, tSv, tDet
    Set tPel = AddTable(page, st, 1.0, 4.4, 4.0, "pelanggan", _
        Array("PK|" & "id_pelanggan|INT AUTO_INCREMENT", _
              "  |nama_pelanggan|VARCHAR(100)", _
              "  |no_telepon|VARCHAR(20)", _
              "U |plat_nomor|VARCHAR(20)", _
              "  |jenis_kendaraan|VARCHAR(50)"), 8)

    Set tSv = AddTable(page, st, 6.0, 4.4, 4.0, "servis", _
        Array("PK|" & "id_servis|INT AUTO_INCREMENT", _
              "FK|id_pelanggan|INT -> pelanggan.id_pelanggan", _
              "  |keluhan|TEXT", _
              "  |biaya_jasa|INT DEFAULT 0", _
              "  |status_servis|ENUM('Antre',...,'Diambil')", _
              "  |tanggal_masuk|DATETIME"), 8)

    Set tDet = AddTable(page, st, 11.0, 4.4, 4.0, "detail_servis", _
        Array("PK|" & "id_detail|INT AUTO_INCREMENT", _
              "FK|id_servis|INT -> servis.id_servis", _
              "FK|id_sparepart|INT -> sparepart.id_sparepart", _
              "  |jumlah|INT DEFAULT 1", _
              "  |subtotal|INT DEFAULT 0"), 8)

    Set tSp = AddTable(page, st, 11.0, 8.9, 4.0, "sparepart", _
        Array("PK|" & "id_sparepart|INT AUTO_INCREMENT", _
              "  |nama_barang|VARCHAR(100)", _
              "  |harga_beli|INT DEFAULT 0", _
              "  |harga_jual|INT DEFAULT 0", _
              "  |stok|INT DEFAULT 0"), 8)

    ' relasi mendatar
    AddRelasiTable page, 5.0, 5.5, 6.0, 5.5, "1", "N", 0.4, 0.35, -0.4, -0.35
    AddRelasiTable page, 10.0, 5.5, 11.0, 5.5, "1", "N", 0.4, 0.35, -0.4, -0.35
    ' relasi vertikal : sparepart -> detail_servis
    AddRelasiTable page, 13.0, 8.9, 13.0, 6.35, "1", "N", -0.55, -0.3, -0.55, 0.3

    AddText page, st, 8.0, 2.2, "PK = Primary Key      FK = Foreign Key      U = Unique", 11, True, C_ERDTXT
    AddText page, st, 8.0, 1.7, _
        "Relasi: pelanggan 1 : N servis  |  servis 1 : N detail_servis  |  sparepart 1 : N detail_servis", _
        10, False, "RGB(89,89,89)"

    Simpan doc, page, "03 ERD - ProBeng"
    st.Close
    doc.Close
End Sub

' ===================================================================
'  FUNGSI BANTU
' ===================================================================
' ---------- helper aman untuk format karakter ----------
Sub SetBold(shp,bold)
    Dim c, err_
    On Error Resume Next
    Set c = shp.CellsU("Char.Style")
    If Err.Number = 0 Then
        If bold Then
            c.FormulaU = "1"
        Else
            c.FormulaU = "0"
        End If
    End If
    Err.Clear
    On Error GoTo 0
End Sub

Sub SetCharColor(shp, colorC)
    On Error Resume Next
    SetCharColor shp, colorC
    Err.Clear
    On Error GoTo 0
End Sub

Sub SetCharSize(shp, fs)
    On Error Resume Next
    SetCharSize shp, fs
    Err.Clear
    On Error GoTo 0
End Sub

' konversi angka ke satuan point
Function Pt(v)
    Dim s
    s = CStr(v)
    s = Replace(s, ",", ".")
    s = Replace(s, " ", "")
    Pt = s & " pt"
End Function

' konversi angka ke satuan inci dengan titik desimal
Function Inch(v)
    Dim s
    s = CStr(v)
    s = Replace(s, ",", ".")
    s = Replace(s, " ", "")
    Inch = s & " in"
End Function

Sub PageSetup(page, w, h)
    page.PageSheet.CellsU("PageWidth").FormulaU = w
    page.PageSheet.CellsU("PageHeight").FormulaU = h
End Sub

Sub Simpan(doc, page, nama)
    Dim vsdxPath, pngPath
    vsdxPath = OUTDIR & nama & ".vsdx"
    pngPath  = OUTDIR & nama & ".png"

    If g_fso.FileExists(vsdxPath) Then g_fso.DeleteFile vsdxPath
    If g_fso.FileExists(pngPath) Then g_fso.DeleteFile pngPath

    doc.SaveAs vsdxPath
    page.Export pngPath
    WScript.Echo "  " & nama & "  ->  .vsdx & .png"
End Sub

Function AddEllipse(page, st, cx, cy, w, h, txt, fillC, lineC, fs)
    Dim s, mEll, ok
    ok = False
    On Error Resume Next
    Set mEll = st.Masters.ItemU("Ellipse")
    If Err.Number <> 0 Then
        WScript.Echo "  [DIAG] gagal ambil master Ellipse: " & Err.Description
        Err.Clear
    Else
        ok = True
    End If
    On Error GoTo 0
    If Not ok Then
        WScript.Echo "  [DIAG] master Ellipse tidak ditemukan"
        Exit Function
    End If
    Set s = page.Drop(mEll, cx, cy)
    s.CellsU("Width").FormulaU = Inch(w)
    s.CellsU("Height").FormulaU = Inch(h)
    s.CellsU("PinX").FormulaU = Inch(cx)
    s.CellsU("PinY").FormulaU = Inch(cy)
    s.CellsU("FillForegnd").FormulaU = fillC
    s.CellsU("FillPattern").FormulaU = "1"
    s.CellsU("LineColor").FormulaU = lineC
    s.CellsU("LineWeight").FormulaU = "2 pt"
    SetCharSize s, fs
    SetCharColor s, "RGB(0,0,0)"
    s.CellsU("VerticalAlign").FormulaU = "1"
    s.Text = txt
    Set AddEllipse = s
End Function

Function AddRect(page, st, x1, y1, x2, y2, txt)
    Dim s
    Set s = page.DrawRectangle(x1, y1, x2, y2)
    s.Text = txt
    Set AddRect = s
End Function

Function AddText(page, st, cx, cy, txt, fs, bold, colorC)
    Dim s
    Set s = page.DrawRectangle(cx - 3.5, cy - 0.2, cx + 3.5, cy + 0.2)
    s.CellsU("FillPattern").FormulaU = "0"
    s.CellsU("LinePattern").FormulaU = "0"
    SetCharSize s, fs
    SetCharColor s, colorC
    SetBold s, bold
    s.CellsU("Para.HorzAlign").FormulaU = "1"
    s.CellsU("VerticalAlign").FormulaU = "1"
    s.Text = txt
    Set AddText = s
End Function

Sub AddActor(page, st, cx, cy, nama)
    Dim head, l1, l2, l3, l4, t
    ' kepala
    Set head = AddEllipse(page, st, cx, cy + 0.62, 0.34, 0.34, "", C_ACTOR, C_ACTOR, 8)
    head.CellsU("LineWeight").FormulaU = "1.25 pt"
    ' badan
    Set l1 = page.DrawLine(cx, cy + 0.44, cx, cy - 0.18)
    FormatLine l1, C_ACTOR, 1.25
    ' tangan
    Set l2 = page.DrawLine(cx - 0.42, cy + 0.24, cx + 0.42, cy + 0.24)
    FormatLine l2, C_ACTOR, 1.25
    ' kaki
    Set l3 = page.DrawLine(cx, cy - 0.18, cx - 0.34, cy - 0.6)
    FormatLine l3, C_ACTOR, 1.25
    Set l4 = page.DrawLine(cx, cy - 0.18, cx + 0.34, cy - 0.6)
    FormatLine l4, C_ACTOR, 1.25
    ' garis dasar
    Dim base
    Set base = page.DrawLine(cx - 0.5, cy - 0.6, cx + 0.5, cy - 0.6)
    FormatLine base, C_ACTOR, 1.25

    Set t = AddText(page, st, cx, cy - 1.02, nama, 10, True, C_ACTOR)
    t.CellsU("Width").FormulaU = "2.4 in"
End Sub

Sub FormatLine(ln, colorC, weight)
    ln.CellsU("LineColor").FormulaU = colorC
    ln.CellsU("LineWeight").FormulaU = Pt(weight)
    ln.CellsU("BeginArrow").FormulaU = "0"
    ln.CellsU("EndArrow").FormulaU = "0"
End Sub

Sub AddPlainLine(page, x1, y1, x2, y2, colorC)
    Dim ln
    Set ln = page.DrawLine(x1, y1, x2, y2)
    FormatLine ln, colorC, 0.9
End Sub

Sub AddArrow(page, sFrom, sTo, label, colorC)
    Dim fx, fy, tx, ty, ln, mid, m

    fx = sFrom.CellsU("PinX").ResultIU
    fy = sFrom.CellsU("PinY").ResultIU
    tx = sTo.CellsU("PinX").ResultIU
    ty = sTo.CellsU("PinY").ResultIU

    Set ln = page.DrawLine(fx, fy, tx, ty)
    ln.CellsU("LineColor").FormulaU = colorC
    ln.CellsU("LineWeight").FormulaU = "1 pt"
    ln.CellsU("BeginArrow").FormulaU = "0"
    ln.CellsU("EndArrow").FormulaU = "13"
    ln.CellsU("EndArrowSize").FormulaU = "1"

    m = (fx + tx) / 2
    Set mid = AddText(page, Nothing, m, (fy + ty) / 2 + 0.16, label, 8, False, colorC)
    mid.CellsU("Width").FormulaU = "1.1 in"
    mid.CellsU("Height").FormulaU = "0.22 in"
End Sub

Sub AddRelasi(page, x1, y1, x2, y2, kiri, kanan, label, oy1, oy2)
    Dim ln
    Set ln = page.DrawLine(x1, y1, x2, y2)
    ln.CellsU("LineColor").FormulaU = C_CLASSTXT
    ln.CellsU("LineWeight").FormulaU = "1.5 pt"

    Dim t1, t2, t3
    Set t1 = AddText(page, Nothing, x1 + 0.35, y1 + 0.35 + oy1, kiri, 11, True, C_CLASSTXT)
    t1.CellsU("Width").FormulaU = "0.6 in"
    Set t2 = AddText(page, Nothing, x2 - 0.35, y2 + 0.35 + oy2, kanan, 11, False, "RGB(89,89,89)")
    t2.CellsU("Width").FormulaU = "0.8 in"
    Set t3 = AddText(page, Nothing, (x1 + x2) / 2, y1 - 0.28, label, 9, False, "RGB(89,89,89)")
    t3.CellsU("Width").FormulaU = "1.8 in"
End Sub

Sub AddRelasiTable(page, x1, y1, x2, y2, kiri, kanan, ox1, oy1, ox2, oy2)
    Dim ln
    Set ln = page.DrawLine(x1, y1, x2, y2)
    ln.CellsU("LineColor").FormulaU = "RGB(51,51,51)"
    ln.CellsU("LineWeight").FormulaU = "1.5 pt"
    ln.CellsU("EndArrow").FormulaU = "13"
    ln.CellsU("EndArrowSize").FormulaU = "2"

    Dim t1, t2
    Set t1 = AddText(page, Nothing, x1 + ox1, y1 + oy1, kiri, 12, True, "RGB(51,51,51)")
    t1.CellsU("Width").FormulaU = "0.5 in"
    Set t2 = AddText(page, Nothing, x2 + ox2, y2 + oy2, kanan, 12, True, "RGB(51,51,51)")
    t2.CellsU("Width").FormulaU = "0.5 in"
End Sub

' ---------- class box (UML) ----------
Function AddClassBox(page, st, x1, yBase, x2, nama, attr, method, fs)
    Dim s, hdrH, nA, nM, i, y, h

    nA = UBound(attr) + 1
    nM = UBound(method) + 1
    hdrH = 0.42
    h = hdrH + (nA * 0.26) + (nM * 0.26) + 0.3

    Set s = page.DrawRectangle(x1, yBase, x2, yBase + h)

    ' header
    Dim hdr
    Set hdr = AddRect(page, st, x1, yBase + h - hdrH, x2, yBase + h, nama)
    hdr.CellsU("FillForegnd").FormulaU = "RGB(217,227,243)"
    hdr.CellsU("FillPattern").FormulaU = "1"
    hdr.CellsU("LineColor").FormulaU = C_CLASSTXT
    hdr.CellsU("LineWeight").FormulaU = "1.5 pt"
    SetCharSize hdr, 12
    SetBold hdr, True
    SetCharColor hdr, C_CLASSTXT
    hdr.CellsU("VerticalAlign").FormulaU = "1"

    ' atribut
    y = yBase + h - hdrH
    For i = 0 To nA - 1
        y = y - 0.26
        Dim t
        Set t = AddText(page, Nothing, (x1 + x2) / 2 + 0.06, y + 0.13, attr(i), fs, False, "RGB(0,0,0)")
        t.CellsU("Width").FormulaU = Inch(x2 - x1 - 0.2)
        t.CellsU("Height").FormulaU = "0.24 in"
        t.CellsU("Para.HorzAlign").FormulaU = "0"
    Next

    ' garis pemisah
    Dim sep
    Set sep = page.DrawLine(x1 + 0.08, y, x2 - 0.08, y)
    sep.CellsU("LineColor").FormulaU = C_CLASSTXT
    sep.CellsU("LineWeight").FormulaU = "1 pt"

    ' method
    For i = 0 To nM - 1
        y = y - 0.26
        Dim t2
        Set t2 = AddText(page, Nothing, (x1 + x2) / 2 + 0.06, y + 0.13, method(i), fs, False, "RGB(0,0,0)")
        t2.CellsU("Width").FormulaU = Inch(x2 - x1 - 0.2)
        t2.CellsU("Height").FormulaU = "0.24 in"
        t2.CellsU("Para.HorzAlign").FormulaU = "0"
    Next

    Set AddClassBox = s
End Function

' ---------- ERD table ----------
Function AddTable(page, st, x1, y1, w, nama, baris, fs)
    Dim s, hdrH, nR, i, y, hdr, isHeader
    nR = UBound(baris) + 1
    hdrH = 0.4
    Dim h
    h = hdrH + (nR * 0.28) + 0.1

    Set s = page.DrawRectangle(x1, y1 - h, x1 + w, y1)
    s.CellsU("FillPattern").FormulaU = "0"
    s.CellsU("LineColor").FormulaU = C_ERDTXT
    s.CellsU("LineWeight").FormulaU = "1.5 pt"

    ' header
    Set hdr = AddRect(page, st, x1, y1 - hdrH, x1 + w, y1, nama)
    hdr.CellsU("FillForegnd").FormulaU = C_ERDTXT
    hdr.CellsU("FillPattern").FormulaU = "1"
    hdr.CellsU("LineColor").FormulaU = C_ERDTXT
    SetCharSize hdr, 12
    SetBold hdr, True
    SetCharColor hdr, "RGB(255,255,255)"
    hdr.CellsU("VerticalAlign").FormulaU = "1"

    y = y1 - hdrH
    For i = 0 To nR - 1
        y = y - 0.28
        Dim parts, tag, namaKol, tipe, t
        parts = Split(baris(i), "|")
        tag = Trim(parts(0))
        namaKol = parts(1)
        tipe = parts(2)

        Dim isi
        isi = tag & "   " & namaKol & "   " & tipe
        Set t = AddText(page, Nothing, x1 + w / 2, y + 0.14, isi, fs, False, "RGB(0,0,0)")
        t.CellsU("Width").FormulaU = Inch(w - 0.2)
        t.CellsU("Height").FormulaU = "0.26 in"
        t.CellsU("Para.HorzAlign").FormulaU = "0"
    If tag <> "" Then SetBold t, True

        ' garis baris
        Dim gl
        Set gl = page.DrawLine(x1 + 0.05, y, x1 + w - 0.05, y)
        gl.CellsU("LineColor").FormulaU = "RGB(200,210,220)"
        gl.CellsU("LineWeight").FormulaU = "0.5 pt"
    Next

    Set AddTable = s
End Function

Main