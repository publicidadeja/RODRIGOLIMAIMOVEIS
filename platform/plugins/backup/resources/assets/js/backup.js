class BackupManagement {
    init() {
        let backupTable = $('#table-backups');
        backupTable.on('click', '.deleteDialog', event => {
            event.preventDefault();

            $('.delete-crud-entry').data('section', $(event.currentTarget).data('section'));
            $('.modal-confirm-delete').modal('show');
        });

        backupTable.on('click', '.restoreBackup', event => {
            event.preventDefault();
            $('#restore-backup-button').data('section', $(event.currentTarget).data('section'));
            $('#restore-backup-modal').modal('show');
        });

        $('.delete-crud-entry').on('click', event =>  {
            event.preventDefault();
            $('.modal-confirm-delete').modal('hide');

            let deleteURL = $(event.currentTarget).data('section');

            $.ajax({
                url: deleteURL,
                type: 'POST',
                data: {'_method': 'DELETE'},
                success: data => {
                    if (data.error) {
                        Srapid.showError(data.message);
                    } else {
                        if (backupTable.find('tbody tr').length <= 1) {
                            backupTable.load(window.location.href + ' #table-backups > *');
                        }

                        backupTable.find('a[data-section="' + deleteURL + '"]').closest('tr').remove();
                        Srapid.showSuccess(data.message);
                    }
                },
                error: data => {
                    Srapid.handleError(data);
                }
            });
        });

        $('#restore-backup-button').on('click', event => {
            event.preventDefault();
            let _self = $(event.currentTarget);
            _self.addClass('button-loading');

            $.ajax({
                url: _self.data('section'),
                type: 'GET',
                success: data => {
                    _self.removeClass('button-loading');
                    _self.closest('.modal').modal('hide');

                    if (data.error) {
                        Srapid.showError(data.message);
                    } else {
                        Srapid.showSuccess(data.message);
                        window.location.reload();
                    }
                },
                error: data => {
                    _self.removeClass('button-loading');
                    Srapid.handleError(data);
                }
            });
        });

        $(document).on('click', '#generate_backup', event => {
            event.preventDefault();
            $('#name').val('');
            $('#description').val('');
            $('#create-backup-modal').modal('show');
        });

        $('#create-backup-modal').on('click', '#create-backup-button', event => {
            event.preventDefault();
            let _self = $(event.currentTarget);
            _self.addClass('button-loading');

            let name = $('#name').val();
            let description = $('#description').val();
            let error = false;
            if (name === '' || name === null) {
                error = true;
                Srapid.showError('Backup name is required!');
            }

            if (!error) {
                $.ajax({
                    url: $('div[data-route-create]').data('route-create'),
                    type: 'POST',
                    data: {
                        name: name,
                        description: description
                    },
                    success: data => {
                        _self.removeClass('button-loading');
                        _self.closest('.modal').modal('hide');

                        if (data.error) {
                            Srapid.showError(data.message);
                        } else {
                            backupTable.find('.no-backup-row').remove();
                            backupTable.find('tbody').append(data.data);
                            Srapid.showSuccess(data.message);
                        }
                    },
                    error: data => {
                        _self.removeClass('button-loading');
                        Srapid.handleError(data);
                    }
                });
            } else {
                _self.removeClass('button-loading');
            }
        });
    }
}

$(document).ready(() => {
    new BackupManagement().init();
});
