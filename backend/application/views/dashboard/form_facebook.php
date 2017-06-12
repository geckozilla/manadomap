
<article>

    <div class="col-md-8" id="page_container">
        <a class="btn" href="<?php echo base_url("exp/getAccessToken.php"); ?>">Ambil data baru</a><hr />
        <div class="conlap">
            <?php foreach($laporan as $l): ?>
                <div class="col-md-12">
                    <h3><a target="_blank" href="<?php echo "https://www.facebook.com/".$l->id_post; ?>">&raquo;&nbsp;&nbsp;<?php echo $l->nama_user; ?></a></h3>
                    <i><?php echo $l->waktu; ?></i>
                    <p style="font-weight: bold;"><?php echo word_limiter($l->message,20); ?></p><br />

                    <a data-l='<?php echo htmlspecialchars(json_encode($l),ENT_QUOTES,"UTF-8"); ?>' href="javascript:void(0);" class="btn green approve"><i class="fa fa-arrow-circle-o-right fa-lg"></i>&nbsp;Setuju</a><a href="javascript:void(0);" class="btn red hapus" data-id="<?php echo $l->id_laporan_facebook; ?>"><i class="fa fa-trash fa-lg"></i>&nbsp;Hapus</a><hr />
                </div>
            <?php endforeach; ?>
        </div>

    </div>
    <div class="col-md-4">

        <div class="conlap">
            <form id="form" action="<?php echo base_url("index.php/dashboard/api_post_laporan/facebook"); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_laporan_facebook" id="id_laporan_facebook">
                <input type="hidden" name="fromBack">
                <p><b>Jenis Laporan</b></p>
                <select id="jenis_laporan" name="jenis_laporan" required>
                    <option value="web">Web</option>
                    <option value="facebook" selected>Facebook</option>
                    <option value="twitter">Twitter</option>
                    <option value="qlue">Qlue</option>
                    <option value="android">Android</option>
                </select><hr />
                <p><b>Nama Pelapor</b></p>
                <input type="text" name="nama_pelapor" id="nama_pelapor" required/><hr />
                <p><b>Alamat Pelapor</b></p>
                <input type="text" name="alamat_pelapor" required/><hr />
                <p><b>Nomor HP Pelapor</b></p>
                <input type="text" name="nomorhp_pelapor" required/><hr />
                <p><b>Email Pelapor</b></p>
                <input type="email" name="email_pelapor" required/><hr />
                <p><b>Kategori</b></p>
                <select name="idkategori_laporan">
                    <option value="">Pilih Kategori</option>
                    <?php
                    foreach($kategori as $kat):
                        ?>
                        <option value="<?php echo $kat->idkategori_laporan; ?>"><?php echo $kat->nama_kategori; ?></option>
                    <?php endforeach;?>
                </select><hr />
                <p><b>Kecamatan</b></p>
                <select id="kecamatan" name="idkecamatan">
                    <option value="">Pilih Kecamatan</option>
                    <?php
                    foreach($records as $kec):
                        ?>
                        <option value="<?php echo $kec->idkecamatan; ?>"><?php echo $kec->nama_kecamatan; ?></option>
                    <?php endforeach;?>
                </select><hr />
                <p><b>Kelurahan</b></p>
                <select id="kelurahan" name="idkelurahan">
                    <option value="">Pilih Kelurahan</option>
                </select><hr />
                <p><b>Koordinat</b></p>
                <div id="latlngpicker" class="normal"></div><br />
                <input name="latitude" type="hidden" id="latitude"/><input name="longitude" type="hidden" id="longitude"/>
                <button type="button" class="btn" id="go_normal">Kembali ke form</button>
                <button id="go_fullscreen" type="button">Tampilan Penuh</button>
                <hr />
                <p><b>Isi Laporan</b></p>
                <textarea name="isi_laporan" style="width: 100%" rows="5" id="isi_laporan" required></textarea><hr />
                <p><b>Foto </b></p>
                <input type="file" name="file_attach" required/><hr />
                <button type="submit" class="btn">Simpan</button>
            </form>
        </div>

    </div>

</article>
<script>
    $(document).ready(function(){

        $(".approve").on("click",function(){
            $thisData = $(this).data();
            $("#nama_pelapor").val($thisData.l.nama_user);
            $("#isi_laporan").val($thisData.l.message);
        });

        $("#latlngpicker").locationpicker({
            location: {
                latitude: 1.493416,
                longitude: 124.891966
            },
            locationName: "",
            radius: 500,
            zoom: 12,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles: [],
            mapOptions: {},
            scrollwheel: true,
            inputBinding: {
                latitudeInput: null,
                longitudeInput: null,
                radiusInput: null,
                locationNameInput: null
            },
            enableAutocomplete: false,
            enableAutocompleteBlur: true,
            autocompleteOptions: null,
            addressFormat: 'postal_code',
            enableReverseGeocode: true,
            draggable: true,
            onchanged: function(currentLocation, radius, isMarkerDropped) {
                console.log(currentLocation);
                $("#latitude").val(currentLocation.latitude);
                $("#longitude").val(currentLocation.longitude);
            },
            onlocationnotfound: function(locationName) {},
            oninitialized: function (component) {},
            markerIcon: undefined,
            markerDraggable: true,
            markerVisible : true
        });

        $("#go_fullscreen").on("click",function(){
            $("#latlngpicker").removeClass("normal").addClass("fullscreen").prop("style","");
            $("#go_normal").show();
        });
        $("#go_normal").on("click",function(){
            $("#latlngpicker").removeClass("fullscreen").addClass("normal").prop("style","width: 100%;height: 300px;overflow: hidden");
            $("#go_normal").hide();
        });

        $("#kecamatan").on("change",function(){

            if($(this).val() !== "") {

                $.get(base_url("index.php/dashboard/api_ambil_kelurahan/" + $(this).val()),function(response){
                    if(response.status == "ok") {
                        kelurahan = $("#kelurahan");
                        kelurahan.html("");
                        kelurahan.append("<option value=''>Pilih Kelurahan</option>");
                        response.data.forEach(function(item){
                            kelurahan.append("<option value='"+item.idkelurahan+"'>"+item.nama_kelurahan+"</option>");
                        });
                    }
                })

            }

        });

        $(".hapus").on("click",function(){
            id = $(this).data().id;
            swal({
                title: "Konfirmasi",
                text: "Apakah anda yakin ingin menghapus item ini?",
                type: "warning",
                showCancelButton: true
            },function(){
                window.location.href = base_url("index.php/dashboard/hapus_laporan_facebook/" + id);
            });
        });


    });
</script>