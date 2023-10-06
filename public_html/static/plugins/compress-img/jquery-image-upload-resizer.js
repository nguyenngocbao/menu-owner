(function ($) {

    $.fn.imageUploadResizer = function (options) {
        var settings = $.extend({
            max_width: 1000,
            max_height: 1000,
            quality: 1,
            do_not_resize: [],
        }, options);
        this.filter('input[type="file"]').each(function () {
            this.onchange = function () {
                const that = this; // input node
                let efiles = [];
                var dataTransfer = new DataTransfer();


                for (let f of this.files) {
                    let originalFile = f;
                    if (!originalFile || !originalFile.type.startsWith('image')) {
                        break;
                    }

                    // Don't resize if doNotResize is set
                    if (settings.do_not_resize.includes('*') || settings.do_not_resize.includes(originalFile.type.split('/')[1])) {
                        return;
                    }

                    var reader = new FileReader();

                    reader.onload = function (e) {
                        var img = document.createElement('img');
                        var canvas = document.createElement('canvas');

                        img.src = e.target.result
                        img.onload = function () {
                            var ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0);
                            //console.log("looop1", img.width, settings.max_width, img.height, settings.max_height);
//                        if (img.width < settings.max_width && img.height < settings.max_height) {
//                            // Resize not required
//                            return;
//                        }
                            //console.log("looop2", img.width);
                            const ratio = Math.min(settings.max_width / img.width, settings.max_height / img.height);
                            const width = Math.round(img.width * ratio);
                            const height = Math.round(img.height * ratio);

                            //canvas.width = width;
                            //canvas.height = height;
                            canvas.width = img.width;
                            canvas.height = img.height;

                            var ctx = canvas.getContext('2d');
                            //ctx.drawImage(img, 0, 0, width, height);
                            ctx.drawImage(img, 0, 0, img.width, img.height);

                            canvas.toBlob(function (blob) {
                            var resizedFile = new File([blob], originalFile.name, originalFile);


                                dataTransfer.items.add(resizedFile);

                                // temporary remove event listener, change and restore
                                var currentOnChange = that.onchange;

                                that.onchange = null;

                                //efiles.push(dataTransfer.files);
                                that.files = dataTransfer.files;
                                console.log("tranfer", dataTransfer.files);
                                that.onchange = currentOnChange;

                            }, 'image/jpeg', settings.quality);
                        }
                    }

                    reader.readAsDataURL(originalFile);
                    //console.log("\n out",originalFile);

                }

                //console.log("\n out file",that.files);

            }
        });

        return this;
    };

}(jQuery));
