class MiniProject {    
    constructor() {
        this.fileInput = document.querySelector('.file-input')
        this.droparea = document.querySelector('.file-drop-area')
        this.fileNameWrapper = document.querySelector('.file-msg')
        this.uploadBtn = document.querySelector('.upload-btn')
        this.btnLoader = document.querySelector('.spinner-border')
        this.btnClearList = document.querySelector('.btn-clear-list')
        this.fileList = document.getElementById('fileList')
        
        this.token = document.head.querySelector('meta[name="csrf-token"]').content
        this.recentUpload = this.getUploadFiles()
    }

    init() {
        this.initLocalSave()
        this.initChannel()
        this.initEventListener()
    }

    initEventListener() {
        this.btnClearList.onclick = () => { 
            if (document.querySelector('.no-data')) return
            if (!confirm('reset file list?')) return
            localStorage.removeItem('upload_data')
            this.fileList.innerHTML = `<tr class="no-data">
                                        <td></td>
                                        <td></td>
                                        <td colspan="2"></td>
                                    </tr>`
        }

        this.uploadBtn.onclick = () => { 
            if(!this.validateFile()) return
            this.btnLoader.classList.remove('d-none')
            const uploadId = this.uuid()
            const files = this.fileInput.files
            
            let arrayFiles = Array.from(files)
            arrayFiles[0]['upload_id'] = uploadId
            arrayFiles[0]['file_name'] = arrayFiles[0].name
            arrayFiles[0]['date'] = new Date().toLocaleString()
            if (this.recentUpload != '') {
                this.recentUpload.push(arrayFiles[0])
                this.setUploadFiles(this.recentUpload)
            } else {
                this.setUploadFiles(arrayFiles)
            }
        
            this.appendFileStatus(files, uploadId)
            this.uploadDocument(files, uploadId)
            this.clearFileInput()
            this.btnLoader.classList.add('d-none')
        }
        
        this.addEventListeners(this.fileInput, ['dragenter', 'focus', 'click'], () => this.droparea.classList.add('is-active'))
        this.addEventListeners(this.fileInput, ['dragleave', 'blur', 'drop'], () => this.droparea.classList.remove('is-active'))
        
        this.fileInput.onchange = (e) => {
            let fileName = e.target.value.split('\\').pop()
            this.fileNameWrapper.textContent = fileName 
        }
    }

    initLocalSave() {
        if (this.recentUpload != '') {
            this.appendFileStatus(this.recentUpload)
        }
    }

    initChannel() {
        window.Echo.channel('UploadChannel')
            .listen('.UploadEvent', (ev) => {
            console.log(ev)
            this.updateFileStatus(ev.upload_id, ev.status);
        })
    }

    clearFileInput() {
        this.fileInput.value = ""
        this.fileNameWrapper.textContent = 'Select file/drag and drop here'
    }
    
    setUploadFiles(uploadFiles) {
        localStorage.setItem('upload_data', JSON.stringify(uploadFiles));
    }
    
    getUploadFiles() {
        return JSON.parse(localStorage.getItem('upload_data')) || '';
    }
    
    addEventListeners(element, events, handler) {
        events.forEach(e => element.addEventListener(e, handler))
    }
    
    validateFile() {
        if (!this.fileInput.files || this.fileInput.files.length === 0) {
            vNotify.error({text: 'Please select a file!', title:'Error'})
            return false
        }
        
        if (!this.fileInput.files[0].name.split('.')[1].match(/csv/i)) {
            vNotify.error({text: 'Csv file only!', title:'Error'})
            return false
        }
    
        return true
    
    }
    
    uploadDocument(files, id) {    
        const fd = new FormData();
        fd.append('csv', files[0]);
        
        fetch('/upload-csv', {
            method: 'POST',
            body: fd,
            headers: {
                'accept' : 'application/json',
                'X-CSRF-TOKEN' : this.token,
                'X-UPLOAD-ID': id
            }
        })
        .catch(err => console.error(err));
    }
    
    appendFileStatus(files, id) {
        let element = ''
        Array.from(files).reverse().forEach((file, key) => {
            const uploadId = file.upload_id || id
            const fileName = file.name || file.file_name
            const date = file.date || new Date().toLocaleString()
            const status = file.status || 'pending'
            const statusClass = this.getStatusClass(status)
            element += `<tr>
                            <td>${date}</td>
                            <td>${fileName}</td>
                            <td colspan="2" upload-id="${uploadId}" class="${statusClass}">
                                ${status}
                                ${status == 'processing' ? '<div class="text-warning spinner-border spinner-border-custom" role="status"></div>' : ''}
                            </td>
                        </tr>`
            
        })
    
        if (document.querySelector('.no-data')) {
            this.fileList.innerHTML = element
        } else {
            this.fileList.insertAdjacentHTML('afterbegin', element)
        }
    }
    
    updateFileStatus(id, status) {
        const statusWrapper = document.querySelector(`[upload-id="${id}"]`)
        if (!statusWrapper) return

        const statusClass = this.getStatusClass(status)
        statusWrapper.className = statusClass
        statusWrapper.innerHTML = `${status} ${status == 'processing' ? '<div class="text-warning spinner-border spinner-border-custom" role="status"></div>' : ''}`
        let localData = this.getUploadFiles()
        if (localData && localData.length > 0) {
            const updatedData = localData.reduce((newData, data, i) => {
                if (data.upload_id == id) {
                    data['status'] = status
                }
                newData.push(data)
                return newData
            }, [])
    
            this.setUploadFiles(updatedData)
        }
    }
    
    getStatusClass(status) {
        let statusClass = ''
        switch (status) {
            case 'processing':
                statusClass = 'text-warning'
            break
            case 'completed':
                statusClass = 'text-success'
            break
            case 'failed':
                statusClass = 'text-danger'
            break
            default:
                statusClass = 'text-light'
        }
    
        return statusClass
    }
    
    uuid(length = 10) {
        let result           = ''
        let characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
        let charactersLength = characters.length
        for (let i = 0; i < length; i++) {
          result += characters.charAt(Math.floor(Math.random() * charactersLength))
       }
    
       return result
    }
    
}