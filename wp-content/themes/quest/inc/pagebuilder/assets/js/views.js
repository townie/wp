/* global Backbone, jQuery, _, wp:true */
var trPbApp = trPbApp || {};

(function (window, Backbone, $, _, trPbApp) {
    'use strict';

    trPbApp.SectionView = Backbone.View.extend({
        template: _.template($('#pt-pb-section-template').html()),
        $editTemplateId: $('#pt-pb-section-edit-template'),
        editTemplate: '',
        $columnTemplateId: $('#pt-pb-insert-column-template'),
        columnTemplate: '',
        className: 'pt-pb-section grid',

        events: {
            'click .pt-pb-section-toggle': 'toggleSection',
            'click .pt-pb-settings-section': 'editSection',
            'click .pt-pb-clone-section': 'cloneSection',
            'click .pt-pb-remove': 'removeSection',
            'click .save-section': 'saveSection',
            'click .pt-pb-insert-column': 'insertColumnsDialog',
            'click .pt-pb-insert-slider': 'insertSlider',
            'click .pt-pb-insert-gallery': 'insertGallery',
            'click .close-reveal-modal': 'closeReveal',
            'click .close-model': 'closeReveal',
            'click .insert .column-layouts li': 'insertColumns',
            'click .update .column-layouts li': 'updateColumns',
            'click .edit-columns': 'insertColumnsDialog'
        },

        initialize: function (options) {
            this.editTemplate = _.template(this.$editTemplateId.html());
            this.columnTemplate = _.template(this.$columnTemplateId.html());
        },

        render: function (cls) {
            this.$el.html(this.template(this.model.toJSON()))
                .attr({
                    'id': this.model.id
                });

            var $content = this.$el.find('.pt-pb-content'),
                $view = this,
                content = this.model.get('content');

            if (content.length > 0) {
                $content.html('');
                _.each(content.models, function (column, ind) {
                    var model = new trPbApp.ColumnModel(column.attributes || {});
                    model.set('parent', $view.model.get('id'));
                    model.set('id', $view.model.id + '__col__' + (ind + 1));
                    $content.append(new trPbApp.ColumnView({
                        model: model
                    }).render().el);
                });
            } else if (content.length === undefined && content.attributes && content.attributes.images) {
                this._addGallery(content);
            } else if (content.length === undefined && content.attributes) {
                this._addSlider(content);
            }

            this.makeColumnsSortable();

            trPbApp.setHiddenInputAll(this.model, this.$el);

            return this;
        },

        editSection: function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.$el.append($('<div />').html(this.editTemplate(this.model.toJSON())).addClass('pt-pb-section-edit reveal-modal'));
            this.$el.find('.reveal-modal').reveal();
        },

        cloneSection: function (e) {
            e.preventDefault();
            e.stopPropagation();
            var model = this.model;
            if (typeof this.model.attributes.content.length === 'undefined') {
                model.attributes['slider'] = {};
                _.each(model.attributes.content.attributes, function (val, key) {
                    if (key === 'slides')
                        return;
                    model.attributes['slider'][key] = val;
                });
                _.each(model.attributes.content.attributes.slides.models, function (val, key) {
                    model.attributes['slider'][key] = val.attributes;
                });
            }
            trPbApp.AddSection(model, trPbApp.Sections.indexOf(this.model) + 1, true, true);
        },

        saveSection: function (e) {
            this.model.set(this.$el.find('form').serializeObject());
            this.closeReveal();
            trPbApp.setHiddenInputAll(this.model, this.$el);
        },

        removeSection: function (e, confirm) {
            e.preventDefault();

            var confirm = confirm ? confirm : window.confirm("Are you sure you want to remove this section ? This step cannot be undone");

            if (confirm) {
                trPbApp.Sections.remove(this.model);
                $(e.target).closest('.pt-pb-section').remove();
            }
        },

        toggleSection: function (e) {
            e.preventDefault();

            var $this = $(e.target),
                $head = $this.closest('.pt-pb-header'),
                $body = $head.siblings('.pt-pb-content-wrap');

            if ($body.css('display') === undefined || $body.css('display') === 'block') {
                $body.slideUp(400, function () {
                    $head.addClass('close');
                });
            } else {
                $body.slideDown(400, function () {
                    $head.removeClass('close');
                });
            }
        },


        insertColumnsDialog: function (e) {
            e.preventDefault();
            e.stopPropagation();
            var edit = e.target.className.match(/fa-|edit-columns/) !== null ? true : false,
                cssClass = edit ? 'pt-pb-insert-columns update reveal-modal' : 'pt-pb-insert-columns insert reveal-modal';
            this.$el.append($('<div />').html(this.columnTemplate()).addClass(cssClass));
            this.$el.find('.pt-pb-column-edit').removeClass('hidden');
            this.$el.find('.reveal-modal').reveal();
        },

        insertColumns: function (e) {
            var $target = e.target.tagName.toUpperCase() === 'LI' ? $(e.target) : $(e.target).closest('li'),
                columns = $target.data('layout').replace(/ /g, '').split(','),
                $view = this,
                $content = this.$el.find('.pt-pb-content'),
                contentArr = new trPbApp.ColumnCollection(),
                order = [];

            $content.html('');

            _.each(columns, function (col, index) {
                var column = {};
                column['parent'] = $view.model.id;
                column['type'] = col;
                column['id'] = $view.model.id + '__col__' + (index + 1);
                if (columns.length === (index + 1))
                    column['last'] = true;
                order.push(column['id']);
                var model = new trPbApp.ColumnModel(column);
                contentArr.add(model);

                $content.append(new trPbApp.ColumnView({
                    model: model
                }).render().el);

            });

            this.model.set('content', contentArr);
            this.closeReveal();

        },

        updateColumns: function (e) {

            var $target = e.target.tagName.toUpperCase() === 'LI' ? $(e.target) : $(e.target).closest('li'),
                columns = $target.data('layout').replace(/ /g, '').split(','),
                $view = this,
                $content = this.$el.find('.pt-pb-content'),
                contentArr = new trPbApp.ColumnCollection(),
                models = $view.model.get('content'),
                order = [];

            if (models.length > columns.length) {
                var confirm = window.confirm("You are about to resize the columns to a lower size than the existing columns, it may remove the last columns and will result in data/module loss. Do you really want to do this ?");
                if (!confirm)
                    return;
            }

            $content.html('');
            _.each(columns, function (col, index) {
                var column = {},
                    model;
                if (models.models[index] !== undefined) {
                    model = models.models[index];
                    model.set('type', col);
                    contentArr.add(model);
                } else {
                    column['parent'] = $view.model.id;
                    column['type'] = col;
                    column['id'] = $view.model.id + '__col__' + (index + 1);
                    order.push(column['id']);
                    model = new trPbApp.ColumnModel(column);
                    contentArr.add(model);
                }

                $content.append(new trPbApp.ColumnView({
                    model: model
                }).render().el);

                if (model.get('content') && model.get('content').attributes !== undefined) {
                    var col = model.get('content');
                    trPbApp.setColumnContent(col.get('parent'), col);
                }

            });

            this.model.set('content', contentArr);
            this.closeReveal();

        },

        makeColumnsSortable: function () {
            var $view = this;
            $view.$el.sortable({
                handle: '.pt-pb-column-sortable',
                placeholder: 'sortable-placeholder pt-pb-column',
                forcePlaceholderSizeType: true,
                distance: 5,
                tolerance: 'pointer',
                items: '.pt-pb-column',
                start: function (e, ui) {
                    var col = ui.item.attr('class').replace(/ ?pt-pb-column ?/, '');
                    ui.placeholder.addClass(col).html('<div class="placeholder-inner" style="height:' + ui.item.height() + 'px"></div>');
                },
                update: function (event, ui) {
                    var updated = [],
                        columns = $view.model.get('content');
                    $(this).find('.pt-pb-column').each(function () {
                        updated.push($(this).attr('id'));
                    });

                    columns.models = _(columns.models).sortBy(function (model) {
                        return _.indexOf(updated, model.get('id'));
                    });

                    $view.model.set('content', columns);
                }
            });
        },

        insertSlider: function (e) {
            e.preventDefault();
            var slider = this._addSlider();
        },

        _addSlider: function (params) {

            var sliderId = this.model.id + '__slider',
                slider = params || new trPbApp.SliderModel({}),
                $content = this.$el.find('.pt-pb-content');

            slider.set({
                'parent': this.model.id,
                'id': sliderId
            });

            $content.html('');

            $content.append(new trPbApp.Modules.SliderView({
                model: slider
            }).render().el);

            this.model.set('content', slider);
            return slider;
        },

        _addGallery: function (params) {
            var galleryId = this.model.id + '__gallery',
                gallery = params || new trPbApp.GalleryModel({}),
                $content = this.$el.find('.pt-pb-content');

            gallery.set({
                'parent': this.model.id,
                'id': galleryId
            });

            $content.html('');

            $content.append(new trPbApp.GalleryView({
                model: gallery
            }).render().el);

            this.model.set('content', gallery);
        },

        insertGallery: function (e) {
            e.preventDefault();
            this._addGallery();
        },

        closeReveal: function () {
            var reveal = this.$el.find('.reveal-modal');
            trPbApp.removeEditor('pt_pb_editor');
            reveal.trigger('reveal:close');
            setTimeout(function () {
                reveal.remove();
            }, 500);
        }


    });

    trPbApp.ColumnView = Backbone.View.extend({
        template: _.template($('#pt-pb-column-template').html()),
        editTemplate: _.template($('#pt-pb-column-edit-template').html()),
        $moduleTemplateId: $('#pt-pb-insert-module-template'),
        moduleTemplate: '',
        className: 'pt-pb-column',
        colClass: 'pt-pb-col-1-1',
        $parent: '',

        events: {
            'click .pt-pb-insert-module': 'insertModuleDialog',
            'click .column-module': 'insertModule',
            'remove-module': 'removeModule'
        },

        initialize: function (options) {
            this.$parent = $('#' + this.model.get('parent'));
            this.colClass = 'pt-pb-col-' + this.model.get('type');
            if (this.model.get('last') === true)
                this.colClass += ' last';
            this.moduleTemplate = _.template(this.$moduleTemplateId.html());
        },

        render: function (cls) {
            this.$el.html(this.template(this.model.toJSON()))
                .attr({
                    'id': this.model.id
                }).addClass(this.colClass);

            var $content = this.$el.children('.pt-pb-column-content'),
                $view = this,
                content = this.model.get('content');
            if (content.attributes !== undefined) {
                $content.html('');
                var module = content.get('type').toProperCase();

                $content.append(new trPbApp.Modules[module + 'View']({
                    model: content
                }).render().el);
            }

            trPbApp.setHiddenInputAll(this.model, this.$el);

            return this;
        },

        insertModuleDialog: function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.$el.append($('<div />').html(this.moduleTemplate({
                modules: trPbApp.ModulesList
            })).addClass('pt-pb-insert-modules reveal-modal'));
            this.$el.find('.reveal-modal').reveal();
        },

        insertModule: function (e) {
            e.preventDefault();
            var $target = $(e.target).hasClass('column-module') ? $(e.target) : $(e.target).parent(),
                module = $target.data('module').replace(/ /g, '').toProperCase();

            if (!trPbApp.Modules[module + 'Model']) return;

            var $col = this,
                $content = this.$el.children('.pt-pb-column-content'),
                atts = {};


            $content.html('');

            atts['parent'] = this.model.id;
            atts['id'] = this.model.id + '__module';
            var model = new trPbApp.Modules[module + 'Model'](atts),
                view = new trPbApp.Modules[module + 'View']({
                    model: model
                });

            $content.append(view.render().el);
            this.model.set('content', model);
            this.closeReveal(true);
            view.editModel();

        },

        removeModule: function (e) {
            var confirm = window.confirm("Are you sure you want to remove this Module ? This step cannot be undone");
            if (confirm) {
                this.model.set('content', []);
                this.render();
            }
        },

        closeReveal: function (immediate) {
            var reveal = this.$el.find('.reveal-modal');
            if (immediate) reveal.remove();
            reveal.trigger('reveal:close');
            setTimeout(function () {
                reveal.remove();
            }, 500);
        },

    });

    trPbApp.Modules.SliderView = Backbone.View.extend({
        template: _.template($('#pt-pb-module-slider-template').html()),
        $editTemplateId: $('#pt-pb-module-slider-edit-template'),
        editTemplate: '',
        className: 'pt-pb-slide',

        events: {
            'click .save-slider': 'updateSlider',
            'click .pt-pb-settings-slider': 'editModel',
            'click .pt-pb-insert-slide': 'insertSlide',
            'click .edit-module-slide .remove': 'removeSlide'
        },

        initialize: function () {
            this.editTemplate = _.template(this.$editTemplateId.html());
            if (this.model.get('slides') === '') {
                this.model.set('slides', new trPbApp.SlideCollection())
            }
        },

        render: function (cls) {
            this.$el.html(this.template(this.model.toJSON()))
                .attr({
                    'id': this.model.id
                });

            var view = this;
            _.each(this.model.get('slides').models, function (slide, ind) {
                view._addSlide(slide);
            });
            this.makeSlidesSortable();
            trPbApp.setHiddenInputAll(this.model, this.$el);
            return this;
        },

        makeSlidesSortable: function () {
            var $view = this;
            $view.$el.sortable({
                handle: '.pt-pb-column-sortable',
                forcePlaceholderSizeType: true,
                distance: 5,
                tolerance: 'pointer',
                items: '.pt-pb-column'
            });
        },

        editModel: function (e) {
            if (e) e.preventDefault();
            this.$el.append($('<div />').html(this.editTemplate(this.model.toJSON())).addClass('pt-pb-slider-edit reveal-modal'));
            this.$el.find('.reveal-modal').reveal();
        },

        updateSlider: function () {
            var $view = this;
            $view.model.set(this.$el.find('.edit-content form').serializeObject());
            $view.$el.find('.admin-label').first().text($view.model.get('admin_label'));
            $view.$el.find('.reveal-modal').trigger('reveal:close');
            trPbApp.setHiddenInputAll(this.model, this.$el);
        },

        insertSlide: function (e) {
            e.preventDefault();
            var slide = this._addSlide(null, true);
        },

        _addSlide: function (params, animate) {
            var slide = params || new trPbApp.SlideModel({}),
                slides = this.model.get('slides'),
                $content = this.$el.find('.slider-container');

            slide.set({
                'parent': this.model.id,
                'id': params ? params.id : this.model.id + '__' + (slides.length + 1)
            });

            slides.add(slide, {
                merge: true
            });
            this.model.set('slides', slides);

            var $slide = $(new trPbApp.Modules.SlideView({
                model: slide
            }).render().el).hide();

            $content.find('.pt-pb-add-slide').before($slide);
            if (animate) {
                $slide.slideDown();
                trPbApp.scrollTo($slide.offset().top - 300);
            } else {
                $slide.show();
            }
            return slide;
        },

        removeSlide: function (e) {
            e.preventDefault();
            var confirm = window.confirm("Are you sure you want to remove this slide ? This step cannot be undone");
            if (confirm) {
                var $slide = $(e.target).closest('.pt-pb-column'),
                    id = $slide.attr('id');

                this.model.get('slides').remove(id);
                $slide.remove();
            }
        }

    });

    trPbApp.Modules.SlideView = Backbone.View.extend({
        template: _.template($('#pt-pb-module-slide-template').html()),
        $editTemplateId: $('#pt-pb-module-slide-edit-template'),
        editTemplate: '',
        className: 'pt-pb-column pt-pb-col-1-1',

        events: {
            'click .save-slide': 'updateSlide',
            'click .edit-module-slide .edit': 'editModel',
            'click .slide-content-preview': 'editModel'
        },

        initialize: function () {
            this.editTemplate = _.template(this.$editTemplateId.html());
        },

        render: function (cls) {
            this.$el.html(this.template(this.model.toJSON()))
                .attr({
                    'id': this.model.id
                });

            trPbApp.setHiddenInputAll(this.model, this.$el);

            return this;
        },

        editModel: function (e) {
            if (e) e.preventDefault();
            this.$el.append($('<div />').html(this.editTemplate(this.model.toJSON())).addClass('pt-pb-slide-edit reveal-modal'));
            this.$el.find('.reveal-modal').reveal();
        },

        updateSlide: function () {

            var $view = this,
                $reveal = $view.$el.find('.reveal-modal');

            $view.model.set($view.$el.find('.edit-content form').serializeObject());
            $reveal.trigger('reveal:close');

            setTimeout(function () {
                $view.render();
            }, 300);
        }

    });

    trPbApp.GalleryView = Backbone.View.extend({
        template: _.template($('#pt-pb-module-gallery-template').html()),
        $editTemplateId: $('#pt-pb-module-gallery-edit-template'),
        editTemplate: '',
        className: 'pt-pb-gimage',

        events: {
            'click .save-gallery': 'updateGallery',
            'click .pt-pb-settings-gallery': 'editModel',
            'click .pt-pb-insert-gimage': 'insertImage',
            'click .edit-module-gimage .remove': 'removeImage'
        },

        initialize: function () {
            this.editTemplate = _.template(this.$editTemplateId.html());
            if (this.model.get('images') === '') {
                var model = new trPbApp.GImageCollection();
                for (var i = 1; i < 5; i++) {
                    var image = new trPbApp.GImageModel({
                        'parent': this.model.id,
                        'id': this.model.id + '__' + i
                    });
                    model.add(image);
                }
                ;

                this.model.set('images', model);
            }

        },

        render: function (cls) {
            this.$el.html(this.template(this.model.toJSON()))
                .attr({
                    'id': this.model.id
                });

            var view = this;
            _.each(this.model.get('images').models, function (image, ind) {
                view._addImage(image, false);
            });
            this.makeImagesSortable();
            trPbApp.setHiddenInputAll(this.model, this.$el);
            return this;
        },

        makeImagesSortable: function () {
            var $view = this;
            $view.$el.sortable({
                handle: '.pt-pb-column-sortable',
                forcePlaceholderSizeType: true,
                distance: 5,
                tolerance: 'pointer',
                items: '.pt-pb-column'
            });
        },

        editModel: function (e) {
            if (e) e.preventDefault();
            this.$el.append($('<div />').html(this.editTemplate(this.model.toJSON())).addClass('pt-pb-gallery-edit reveal-modal'));
            this.$el.find('.reveal-modal').reveal();
        },

        updateGallery: function () {
            var $view = this;
            $view.model.set(this.$el.find('.edit-content form').serializeObject());
            $view.$el.find('.admin-label').first().text($view.model.get('admin_label'));
            $view.$el.find('.reveal-modal').trigger('reveal:close');
            trPbApp.setHiddenInputAll(this.model, this.$el);
        },

        insertImage: function (e) {
            if (e) {
                e.preventDefault();
            }
            var slide = this._addImage(null, true);
        },

        _addImage: function (params, animate) {
            var image = params || new trPbApp.GImageModel({}),
                images = this.model.get('images'),
                $content = this.$el.find('.images-container');

            image.set({
                'parent': this.model.id,
                'id': params ? params.id : this.model.id + '__' + (images.length + 1)
            });

            images.add(image, {
                merge: true
            });
            this.model.set('images', images);

            var $image = $(new trPbApp.GImageView({
                model: image
            }).render().el).hide();

            $content.append($image);

            if (animate) {
                $image.slideDown();
                trPbApp.scrollTo($image.offset().top - 100);
            } else {
                $image.show();
            }
            return image;
        },

        removeImage: function (e) {
            e.preventDefault();
            var confirm = window.confirm("Are you sure you want to remove this Image ? This step cannot be undone");
            if (confirm) {
                var $image = $(e.target).closest('.pt-pb-column'),
                    id = $image.attr('id');
                this.model.get('images').remove(id);
                $image.remove();
            }
        }

    });

    trPbApp.GImageView = Backbone.View.extend({
        template: _.template($('#pt-pb-module-gimage-template').html()),
        $editTemplateId: $('#pt-pb-module-gimage-edit-template'),
        editTemplate: '',
        className: 'pt-pb-column pt-pb-col-1-4',

        events: {
            'click .save-gimage': 'updateImage',
            'click .edit-module-gimage .edit': 'editModel',
            'click .gimage-content-preview': 'editModel'
        },

        initialize: function () {
            this.editTemplate = _.template(this.$editTemplateId.html());
        },

        render: function (cls) {
            this.$el.html(this.template(this.model.toJSON()))
                .attr({
                    'id': this.model.id
                });

            trPbApp.setHiddenInputAll(this.model, this.$el);

            return this;
        },

        editModel: function (e) {
            if (e) e.preventDefault();
            this.$el.append($('<div />').html(this.editTemplate(this.model.toJSON())).addClass('pt-pb-slide-edit reveal-modal'));
            this.$el.find('.reveal-modal').reveal();
        },

        updateImage: function () {

            var $view = this,
                $reveal = $view.$el.find('.reveal-modal');

            $view.model.set($view.$el.find('.edit-content form').serializeObject());
            $reveal.trigger('reveal:close');

            setTimeout(function () {
                $view.render();
            }, 300);
        }

    });

    trPbApp.Modules.ImageView = Backbone.View.extend({
        template: _.template($('#pt-pb-module-image-template').html()),
        $editTemplateId: $('#pt-pb-module-image-edit-template'),
        editTemplate: '',

        events: {
            'click .save-image': 'updateImage',
            'click .edit-module .edit': 'editModel',
            'click .content-preview': 'editModel',
            'click .edit-module .remove': 'removeModel'
        },

        initialize: function () {
            this.editTemplate = _.template(this.$editTemplateId.html());
        },

        render: function (cls) {
            this.$el.html(this.template(this.model.toJSON()))
                .attr({
                    'id': this.model.id
                });
            trPbApp.setHiddenInputAll(this.model, this.$el);
            return this;
        },

        editModel: function (e) {
            if (e) e.preventDefault();
            this.$el.append($('<div />').html(this.editTemplate(this.model.toJSON())).addClass('pt-pb-image-edit reveal-modal'));
            this.$el.find('.reveal-modal').reveal();
        },

        removeModel: function (e) {
            e.preventDefault();
            $('#' + this.model.get('parent')).trigger('remove-module')
        },

        updateImage: function () {

            var id = this.model.get('id'),
                parent = this.model.get('parent'),
                view = this;

            this.model.set(this.$el.find('.edit-content form').serializeObject());

            trPbApp.setColumnContent(parent, this.model);

            this.$el.find('.reveal-modal').trigger('reveal:close');

            setTimeout(function () {
                view.render();
            }, 300);
        }

    });


    trPbApp.Modules.TextView = Backbone.View.extend({
        template: _.template($('#pt-pb-module-text-template').html()),
        $editTemplateId: $('#pt-pb-module-text-edit-template'),
        editTemplate: '',

        events: {
            'click .content-preview': 'editModel',
            'click .save-text': 'updateContent',
            'click .edit-module .edit': 'editModel',
            'click .edit-module .remove': 'removeModel'
        },

        initialize: function () {
            this.editTemplate = _.template(this.$editTemplateId.html());
        },

        render: function (cls) {
            this.$el.html(this.template(this.model.toJSON()))
                .attr({
                    'id': this.model.id
                });
            trPbApp.setHiddenInputAll(this.model, this.$el);
            return this;
        },

        editModel: function (e) {
            if (e) e.preventDefault();
            this.$el.append($('<div />').html(this.editTemplate(this.model.toJSON())).addClass('pt-pb-image-edit reveal-modal'));
            trPbApp.createEditor(this.$el, this.model.get('content'));
            this.$el.find('.reveal-modal').reveal();
        },

        removeModel: function (e) {
            e.preventDefault();
            $('#' + this.model.get('parent')).trigger('remove-module')
        },

        updateContent: function (e) {
            var id = this.model.get('id'),
                parent = this.model.get('parent'),
                view = this;

            this.model.set(this.$el.find('.edit-content form').serializeObject());
            this.model.set('content', trPbApp.getContent());

            trPbApp.setColumnContent(parent, this.model);

            this.$el.find('.reveal-modal').trigger('reveal:close');
            trPbApp.removeEditor('pt_pb_editor');
            setTimeout(function () {
                view.render();
            }, 300);
        }

    });


    trPbApp.Modules.HovericonView = Backbone.View.extend({
        template: _.template($('#pt-pb-module-hovericon-template').html()),
        $editTemplateId: $('#pt-pb-module-hovericon-edit-template'),
        editTemplate: '',

        events: {
            'click .content-preview': 'editModel',
            'click .save-icon': 'updateModel',
            'click .edit-module .edit': 'editModel',
            'click .edit-module .remove': 'removeModel'
        },

        initialize: function () {
            this.editTemplate = _.template(this.$editTemplateId.html());
        },

        render: function (cls) {
            this.$el.html(this.template(this.model.toJSON()))
                .attr({
                    'id': this.model.id
                });
            trPbApp.setHiddenInputAll(this.model, this.$el);
            return this;
        },

        editModel: function (e) {
            if (e) e.preventDefault();
            this.$el.append($('<div />').html(this.editTemplate(this.model.toJSON())).addClass('pt-pb-hovericon-edit reveal-modal'));
            trPbApp.createEditor(this.$el, this.model.get('content'));
            this.$el.find('.reveal-modal').reveal();

        },

        removeModel: function (e) {
            e.preventDefault();
            $('#' + this.model.get('parent')).trigger('remove-module');
        },

        updateModel: function (e) {
            var id = this.model.get('id'),
                parent = this.model.get('parent'),
                view = this;

            this.model.set(this.$el.find('.edit-content form').serializeObject());
            this.model.set('content', trPbApp.getContent());

            trPbApp.setColumnContent(parent, this.model);

            this.$el.find('.reveal-modal').trigger('reveal:close');
            trPbApp.removeEditor('pt_pb_editor');
            setTimeout(function () {
                view.render();
            }, 300);
        }

    });


})(window, Backbone, jQuery, _, trPbApp);