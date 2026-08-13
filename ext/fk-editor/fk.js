/**
 * FK标记语言编辑器
 * 创建：2019-11-05
 * 更新：2022-09-26
 */
'use strict';
var SX = SX || {};
SX.fkEditor = function (id, callback) {
	class fkEditor {
		constructor(id) {
			var _this = this;
			var textarea = SX(id);
			if (!textarea.length) return;
			this.domain = textarea.attr('data-domain') == 'true';
			textarea = textarea[0];
			//创建main容器
			var fkMain = SX.c('div');
			fkMain.className = 'fk-editor-main';
			//创建菜单
			var fkMenu = SX.c('div');
			fkMenu.className = 'fk-editor-menu';
			this.config = {};
			this.config.menu = [
				{ name: '__FK快捷键', fk: 'key' },
				{ name: '颜色', fk: '#4f9552' },
				{ name: '大小', fk: '~20' },
				{ name: '加粗', fk: 'b' },
				{ name: '斜体', fk: 'i' },
				{ name: '标签', fk: '$[{text}]' },
				{ name: '链接', fk: 'link' },
				{ name: '图片', fk: 'img' },
				{ name: '文件', fk: 'file' },
				{ name: '下划线', fk: 'u' },
				{ name: '删除线', fk: 's' },
				{ name: '水平线', fk: '$==========' },
				{ name: '有序列表', fk: 'orderedList' },
				{ name: '无序列表', fk: 'unorderedList' },
				{ name: '表格', fk: 'table' },
				{ name: '多行表格', fk: 'tables' },
				{ name: '代码', fk: 'code' },
				{ name: '多行代码', fk: 'codes' },
				{ name: '上传图片', fk: 'uploadImg' },
				{ name: '上传文件', fk: 'uploadFile' }
			];
			this.config.menu.forEach((v) => {
				if (v.fk == 'uploadImg') {
					fkMenu.innerHTML += '<label data-fk="' + v.fk + '" id="upload-img">上传图片</label>';
				} else if (v.fk == 'uploadFile') {
					fkMenu.innerHTML += '<label data-fk="' + v.fk + '" id="upload-file">上传文件</label>';
				} else {
					fkMenu.innerHTML += '<label data-fk="' + v.fk + '">' + v.name + '</label>';
				}
			})
			fkMain.append(fkMenu);
			SX('label', fkMenu).click(function () {
				SX.sp()
				_this.runFka(this.getAttribute('data-fk'));
			})
			//创建内容区
			var _textarea = textarea.cloneNode(true);
			fkMain.append(_textarea);
			//创建状态栏
			var fkStatus = SX.c('div');
			fkStatus.className = 'fk-editor-status';
			fkStatus.innerHTML = '<label>总行:<span>1</span></label><label>总字:<span>0</span></label><label>已选: <span>0</span></label><label>当前行: <span>0</span></label><label>当前列: <span>0</span></label>';
			fkMain.append(fkStatus);
			textarea.parentNode.replaceChild(fkMain, textarea);
			textarea = _textarea;
			this.textarea = textarea;
			fkStatus = SX('span', fkStatus);
			var sLines = fkStatus[0];
			var sCount = fkStatus[1];
			var sSel = fkStatus[2];
			var sLine = fkStatus[3];
			var sCol = fkStatus[4];
			this.selText = '';
			this.log = { l: [{ val: textarea.value, pos: textarea.selectionStart }], r: [] };
			//设置状态
			this.setStatus = function () {
				var val = textarea.value;
				var textLine = val.substr(0, textarea.selectionStart).split('\n');
				sLines.innerHTML = val.split('\n').length;
				sCount.innerHTML = val.length;
				sSel.innerHTML = _this.getSel().length;
				sLine.innerHTML = textLine.length;
				sCol.innerHTML = textLine[textLine.length - 1].length;
			}
			this.setStatus();
			sLine.innerHTML = 0;
			textarea.onkeydown = function (e) {
				//支持tab键								
				if (e.code == 'Tab') {
					e.preventDefault();
					_this.setValue(textarea, '    ');
				}
				//换行对齐上一行的首空格，如果上一行不存在则对齐下一行
				else if (!e.ctrlKey && !e.shiftKey && e.code === 'Enter') {
					_this.setStatus();
					var arr = textarea.value.split('\n'), s = +(sLine.innerHTML), text = arr[s - 1];
					text = text.match(/^[\s\t]+/);
					if (text) {
						text = text[0];
					} else {
						text = arr[s] ? arr[s].match(/^[\s\t]+/) : false;
						text = text ? text[0] : '';
					}
					//输入结束后才能设置
					if (!e.isComposing) {
						e.preventDefault();
						_this.setValue(textarea, '\n' + text);
					}
				}
				// 1-6标题
				else if (e.ctrlKey && !e.shiftKey && e.key === '1') _this.setFka('$#1 ')
				else if (e.ctrlKey && !e.shiftKey && e.key === '2') _this.setFka('$# ')
				else if (e.ctrlKey && !e.shiftKey && e.key === '3') _this.setFka('$#3 ')
				else if (e.ctrlKey && !e.shiftKey && e.key === '4') _this.setFka('$#4 ')
				else if (e.ctrlKey && !e.shiftKey && e.key === '5') _this.setFka('$#5 ')
				else if (e.ctrlKey && !e.shiftKey && e.key === '6') _this.setFka('$#6 ')
				// 粗体
				else if (e.ctrlKey && !e.shiftKey && e.code === 'KeyB') _this.setFka('b')
				// 斜体
				else if (e.ctrlKey && !e.shiftKey && e.code === 'KeyI') _this.setFka('i')
				// 大小
				else if (e.ctrlKey && !e.shiftKey && e.code === 'KeyQ') _this.setFka('~20')
				// 下划线
				else if (e.ctrlKey && !e.shiftKey && e.code === 'KeyU') _this.setFka('u')
				// 标签
				else if (e.ctrlKey && !e.shiftKey && e.code === 'KeyE') _this.setFka('$[{text}]')
				// 颜色
				else if (e.ctrlKey && !e.shiftKey && e.code === 'KeyY') _this.setFka('#4f9552')
				// 水平线
				else if (e.ctrlKey && !e.shiftKey && e.code === 'Enter') _this.setFka('$============================================\n')
				// 链接
				else if (e.ctrlKey && !e.shiftKey && e.code === 'KeyL') _this.setFka('$[{text}](https://)', -1)
				// 图片
				else if (e.ctrlKey && !e.shiftKey && e.code === 'KeyP') _this.setFka('img')
				// 文件
				else if (e.ctrlKey && !e.shiftKey && e.code === 'KeyF') _this.setFka('file')
				// 撤销
				else if (e.ctrlKey && !e.altKey && e.code === 'KeyZ') _this.unmakeLog()
				// 取消撤销
				else if (e.ctrlKey && e.altKey && e.code === 'KeyZ') _this.cancelUnmakeLog()
				// 无序列表
				else if (e.ctrlKey && !e.shiftKey && e.code === 'KeyO') _this.runFka('unorderedList')
				// 有序列表
				else if (e.ctrlKey && e.shiftKey && e.code === 'KeyO') _this.runFka('orderedList')
				// 代码
				else if (e.ctrlKey && !e.shiftKey && e.code === 'Slash') _this.runFka('code')
				// 多行代码
				else if (e.ctrlKey && e.shiftKey && e.code === 'Slash') _this.runFka('codes')
				// 表格
				else if (e.ctrlKey && !e.shiftKey && e.code === 'KeyR') _this.runFka('table')
				// 多行表格
				else if (e.ctrlKey && e.shiftKey && e.code === 'KeyR') _this.runFka('tables')
				// 图片上传
				else if (e.ctrlKey && !e.shiftKey && e.code === 'KeyD') SX('#upload-img')[0].click()
				// 文件上传
				else if (e.ctrlKey && e.shiftKey && e.code === 'KeyD') SX('#upload-file')[0].click()
				// 保存提交
				else if (e.ctrlKey && !e.shiftKey && e.code === 'KeyS') {
					var submit = SX('input[type=submit]');
					if (submit.length) submit[0].click();
				}
				// 屏蔽ctrl键，防止与浏览器自带浏览器冲突
				if (e.ctrlKey && e.code !== 'F5' && e.code !== 'KeyA' && e.code !== 'KeyX' && e.code !== 'KeyC' && e.code !== 'KeyV') e.preventDefault();
				_this.setStatus();
			}
			textarea.oninput = function () {
				_this.setLog();
				_this.setStatus();
			}
			textarea.onblur = function () {
				sLine.innerHTML = sCol.innerHTML = 0;
			}
			textarea.onmousedown = function () {
				window.onmousemove = function () {
					_this.setStatus();
					_this.selText = _this.getSel();
				}
				window.onmouseup = function () {
					_this.setStatus();
					window.onmousemove = window.onmouseup = null;
					setTimeout(() => {
						sSel.innerHTML = _this.getSel().length;
					}, 50);
				}
			}
			window.onmousedown = function (e) {
				sSel.innerHTML = 0;
				if (e.path && !e.path[0].tagName == 'LABEL') _this.selText = '';
			}
			textarea.onkeyup = function () {
				sSel.innerHTML = _this.getSel().length;
				_this.selText = _this.getSel();
			}
			callback && callback.call(_this, textarea);
		}
		/**
		 * 记录日志
		 */
		setLog() {
			this.log.l.push({
				val: this.textarea.value,
				pos: this.textarea.selectionStart
			})
		}
		/**
		 * 撤销日志
		 */
		unmakeLog() {
			if (this.log.l.length) {
				this.log.r.push(this.log.l.pop());
				if (this.log.l.length) {
					let obj = this.log.l[this.log.l.length - 1];
					this.textarea.value = obj.val;
					this.textarea.setSelectionRange(obj.pos, obj.pos);
				}
			}
		}
		/**
		 * 取消撤销日志
		 */
		cancelUnmakeLog() {
			if (this.log.r.length) {
				this.log.l.push(this.log.r.pop());
				if (this.log.r.length) {
					let obj = this.log.l[this.log.l.length - 1];
					this.textarea.value = obj.val;
					this.textarea.setSelectionRange(obj.pos, obj.pos);
				}
			}
		}
		/**
		 * 运行相关指令
		 * @param string t 
		 */
		runFka(t) {
			if (t == 'key') {
				if (SX('.fk-editor-key').length) {
					SX('.fk-editor-key').show();
				} else {
					var fkKey = SX.c('div');
					fkKey.className = 'fk-editor-key';
					fkKey.innerHTML = '<p>__FK快捷键Ctrl+b=粗体Ctrl+1=1号标题Ctrl+i=斜体Ctrl+2=2号标题Ctrl+q=大小Ctrl+3=3号标题Ctrl+u=下划线Ctrl+4=4号标题Ctrl+e=标签Ctrl+5=5号标题Ctrl+l=链接Ctrl+6=6号标题Ctrl+p=图片Ctrl+f=文件Ctrl+y=颜色Ctrl+Enter=水平线Ctrl+z=撤销Ctrl+Shift+z=取消撤销Ctrl+/=代码Ctrl+Shift+/=多行代码Ctrl+r=表格Ctrl+Shift+r=多行表格Ctrl+o=无序列表Ctrl+Shift+o=有序列表Ctrl+d=上传图片Ctrl+Shift+d=上传文件Ctrl+s=保存内容</p>'.replace(/\+/g, '</span><span>').replace(/=/g, '</span>').replace(/Ctrl/g, '</p><p><span>Ctrl');
					document.body.appendChild(fkKey);
					SX.on(window, 'click', function (e) {
						SX('.fk-editor-key').hide();
					})
				}
			} else if (t == 'link') {
				this.setFka('$[{text}](https://)', -1)
			} else if (t == 'code') {
				this.setFka('$`{text}`')
			} else if (t == 'codes') {
				this.setFka('$``fk\n{text}\n``')
			} else if (t == 'table') {
				this.setFka('$| 标题 | 内容\n----------------\n| 标题 | 内容\n| 标题 | 内容')
			} else if (t == 'tables') {
				this.setFka('$[table\n  180 标题 | 内容\n  ----------------\n  标题\n  --\n  内容\n]')
			} else if (t == 'orderedList' || t == 'unorderedList') { //有序列表、无序列表
				this.selText = this.selText.split('\n');
				this.selText.forEach((v, k) => {
					this.selText[k] = (t == 'orderedList' ? '* ' : '- ') + v;
				})
				this.setFka('$' + this.selText.join('\n'));
			} else if (t == 'uploadImg' || t == 'uploadFile') { //上传
				var _this = this;
				sx.upload({
					accept:t == 'uploadImg' ? 'image/*' : '*',
					domain:_this.domain,
					multiple:true,
					error(){
						sx.alert(res.message);
					},
					success(e){
						console.log(e);
						e.forEach(v=>{
							_this.setValue(_this.textarea, t == 'uploadImg' ? `[img ${v.url}]` : `[file ${v.url} ${v.name}]`);
						})
					},
				})
			} else {
				this.setFka(t);
			}
		}
		/**
		 * 设置fk标记符，如果没有选中文本，光标在[]中
		 * fk命令语法：fk标记符 或 $自定义{text}
		 * {text}为占位符，表示选中的文本
		 * @param string 标记符或自定义文本
		 * @param int pos 光标偏移量，光标默认在文本尾部，负值从后向前偏移
		 */
		setFka(fk, pos = 0) {
			fk = fk.replace(/\\n/g, '\n');
			if (!this.selText.length && fk[0] != '$') pos = -1;
			this.setValue(this.textarea, fk[0] == '$' ? fk.substr(1).replace('{text}', this.selText) : `[${fk} ${this.selText}]`, pos);
			this.setStatus();
			this.selText = '';
		}
		/**
		 * 在光标处插入字符串
		 * @param object el 对象
		 * @param string val 值
		 * @param int offset 光标偏移量
		 */
		setValue(el, val, offset = 0) {
			let pos = el.selectionStart + val.length + offset, top = el.scrollTop;
			el.value = el.value.substr(0, el.selectionStart) + val + el.value.substr(el.selectionEnd);
			el.focus();
			el.setSelectionRange(pos, pos);
			el.scrollTop = top;
			this.setLog();
		}
		/**
		 * 获取鼠标选中的文本
		 * @returns string
		 */
		getSel() {
			return window.getSelection().toString();
		}
	}
	return new fkEditor(id, callback);
}