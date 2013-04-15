Array.prototype.eraseMy = function(item)
{
	for ( var i = this.length; i--;)
	{
		if (this[i] === item)
			this.splice(i, 1);
	}
	return this;
};

var ls = ls ||
{};

ls.cw = ls.cw ||
{};

/**
 * Обработка комментариев
 */
ls.cw.core = (function($)
{
	this.options =
	{
		bTemplateAction : true,
		autoupdate_period : 10,
		no_wont_reply : 0,
		classes :
		{
			form_loader : 'loader',
			comment : 'comment',
			reply_link : 'reply-link',
			answer_direct : 'cw-answer-direct',
			answer_later : 'cw-answer-later',
			action_reply_later : 'cw-action-reply-later',
			action_wont_reply : 'cw-action-wont-reply',
			new_comments_counter : 'new_comments_counter',
			direct_comments_counter : 'direct_comments_counter',
			later_comments_counter : 'later_comments_counter'
		},
		ids :
		{
			new_comments_counter : 'new_comments_counter',
			direct_comments_counter : 'direct_comments_counter',
			later_comments_counter : 'later_comments_counter'
		},
		captions :
		{
			wont_reply : 'Не буду отвечать',
			reply_later : 'Отвечу позже',
		},
		actions :
		{
			wont_reply : 'ls.cw.core.toggleAnswer(this,__COMMENTID__,"wontreply"); return false;',
			reply_later : 'ls.cw.core.toggleAnswer(this,__COMMENTID__,"replylater"); return false;',
		}
	}

	this.getComment = function(comment_id)
	{
		return $('#comment_id_' + comment_id);
	}

	this.searchInArray = function(val, arr)
	{
		var idx = arr.indexOf(parseInt(val));
		if (idx == -1)
			idx = arr.indexOf(val.toString());

		return idx;
	}

	this.removeNewComment = function(comment_id)
	{
		var idx = this.searchInArray(comment_id, ls.comments.aCommentNew);
		if (idx != -1)
		{
			ls.comments.aCommentNew.eraseMy(ls.comments.aCommentNew[idx]);
			ls.comments.setCountNewComment(ls.comments.aCommentNew.length);
			cmt = this.getComment(comment_id);
			if (cmt.length)
			{
				cmt.removeClass(ls.comments.options.classes.comment_new);
			}
		}
	}

	this.updatePanel = function(sCT, iCID, bNotShowProcess)
	{
		if (!sCT)
			sCT = '';
		if (!iCID)
			iCID = 0;

		if (!bNotShowProcess)
        {
			$('#commentwatcher-panel').addClass('active');
            $('#commentwatcher-toolbar').addClass('active');
        }
		ls.ajax(aRouter['commentwatcher'] + 'ajaxupdatepanel/',
		{
			'sContainerType' : sCT,
			'iContainerId' : iCID
		}, function(result)
		{
			if (!bNotShowProcess)
            {
				$('#commentwatcher-panel').removeClass('active');
                $('#commentwatcher-toolbar').removeClass('active');
            }
			if (!result)
			{
				ls.msg.error('Error', 'Please try again later');
			}
			else
			{
				if (result.bStateError)
				{
					ls.msg.error(null, result.sMsg);
				}
				else
				{
					$('#commentwatcher-activities').html(result.sCWPanelContent);
				}
			}
		}.bind(this));
	}

	this.toggleAnswer = function(actObj, comment_id, type)
	{

		thisObj = this;
		ls.ajax(aRouter['commentwatcher'] + 'ajax' + type + '/',
		{
			idComment : comment_id
		}, function(result)
		{
			if (!result)
			{
				ls.msg.error('Error', 'Please try again later');
			}
			else
			{
				if (result.bStateError)
				{
					ls.msg.error(null, result.sMsg);
				}
				else
				{

					cmt = thisObj.getComment(comment_id);

					cmt.removeClass(this.options.classes.answer_direct);
					cmt.removeClass(this.options.classes.answer_later);
					if (type == 'replylater')
					{
						cmt.addClass(this.options.classes.answer_later);
					}

					if ($(actObj).length)
						$(actObj).remove();

					this.removeNewComment(comment_id);
					this.refreshCommentWatcher();
				}
			}
		}.bind(this));
	}

	this.insertAction = function(parentAction, sTitle, sAction, sClassName)
	{
		if (this.options.bTemplateAction)
			return;

		var newAct = $('<a>',
		{
			'href' : '#',
			'onclick' : sAction
		}).html(sTitle);

		newAct.addClass(sClassName);
		if (parentAction.parent().is('li'))
		{
			parentAction.parent().after($('<li>').html(newAct));
		}
		else
		{
			parentAction.after(newAct);
		}
	}

	this.removeAction = function(comment_id, sClassName)
	{
		var comm = this.getComment(comment_id);
		if (comm.length)
		{
			var act = comm.find('.' + sClassName);
			if (act.length)
			{
				if (act.parent().is('li'))
				{
					act.parent().remove();
				}
				else
				{
					act.remove();
				}
			}
		}
	}

	this.makeAnswerDirect = function(comment_id)
	{
		var thisObj = this;
		var comm = this.getComment(comment_id);
		if (comm.length)
		{
			comm.addClass(this.options.classes.answer_direct);

			if (this.options.bTemplateAction)
				return;

			if (!thisObj.options.no_wont_reply)
			{
				var act = comm.find('.' + this.options.classes.reply_link);
				if (act.length)
				{
					thisObj.insertAction(act, thisObj.options.captions.wont_reply, thisObj.options.actions.wont_reply.replace('__COMMENTID__', comment_id), this.options.classes.action_wont_reply);
				}
			}
		}
	}

	this.makeAnswerLater = function(comment_id)
	{
		var thisObj = this;
		var comm = this.getComment(comment_id);
		if (comm.length)
		{
			comm.addClass(this.options.classes.answer_later);

			if (this.options.bTemplateAction)
				return;

			thisObj.removeAction(comment_id, thisObj.options.classes.action_reply_later);
			var act = comm.find('.' + this.options.classes.reply_link);
			if (act.length)
			{
				thisObj.insertAction(act, thisObj.options.captions.wont_reply, thisObj.options.actions.wont_reply.replace('__COMMENTID__', comment_id), this.options.classes.action_wont_reply);
			}
		}
	}

	this.addActionAnswerLater = function(comment_id)
	{
		if (this.options.bTemplateAction)
			return;

		var thisObj = this;
		var comm = this.getComment(comment_id);
		if (comm.length && !comm.hasClass(this.options.classes.answer_later))
		{
			var act = comm.find('.' + this.options.classes.reply_link);
			if (act.length)
			{
				thisObj.insertAction(act, thisObj.options.captions.reply_later, thisObj.options.actions.reply_later.replace('__COMMENTID__', comment_id), this.options.classes.action_reply_later);
			}
		}
	}

	// Устанавливает ответов прямые
	this.setCountDirectComment = function(count)
	{
		dcc = $('#' + this.options.ids.direct_comments_counter);
		if (dcc.length)
		{
			if (count > 0)
			{
				dcc.css('display', 'block').text(count);
			}
			else
			{
				dcc.text(0).hide();
			}
		}
	}

	// Устанавливает комментариев помеченных к ответу позже
	this.setCountLaterComment = function(count)
	{
		dcc = $('#' + this.options.ids.later_comments_counter);
		if (dcc.length)
		{
			if (count > 0)
			{
				dcc.css('display', 'block').text(count);
			}
			else
			{
				dcc.text(0).hide();
			}
		}
	}

	this.goToNextDirectAnswer = function()
	{
		thisObj = this;
		nid = thisObj.aCommentDirect[thisObj.iCurrentDirect + 1];
		if (!nid)
		{
			thisObj.iCurrentDirect = 0;
		}
		else
			thisObj.iCurrentDirect++;

		nid = thisObj.aCommentDirect[thisObj.iCurrentDirect];
		if (nid)
		{
			ls.comments.scrollToComment(nid);
			if (thisObj.options.no_wont_reply)
				thisObj.toggleAnswer(null, nid, 'wontreply');
			thisObj.removeNewComment(nid);
		}
		else
		{
			return;
		}
	}

	this.goToNextLaterAnswer = function()
	{
		thisObj = this;
		nid = thisObj.aCommentLater[thisObj.iCurrentLater + 1];
		if (!nid)
		{
			thisObj.iCurrentLater = 0;
		}
		else
			thisObj.iCurrentLater++;

		nid = thisObj.aCommentLater[thisObj.iCurrentLater];
		if (nid)
		{
			ls.comments.scrollToComment(nid);
			thisObj.removeNewComment(nid);
		}
		else
		{
			return;
		}
	}

	this.refreshCommentWatcher = function()
	{
		thisObj = this;
		curDirect = this.aCommentDirect[this.iCurrentDirect];
		nextDirect = this.aCommentDirect[this.iCurrentDirect + 1];
		curLater = this.aCommentDirect[this.iCurrentLater];
		nextLater = this.aCommentDirect[this.iCurrentLater + 1];

		this.aCommentDirect = [];
		this.aCommentLater = [];

		$('.' + this.options.classes.comment + '.' + this.options.classes.answer_direct).each(function(index, item)
		{
			thisObj.aCommentDirect.push(parseInt($(item).attr('id').replace('comment_id_', '')));
		});
		$('.' + this.options.classes.comment + '.' + this.options.classes.answer_later).each(function(index, item)
		{
			thisObj.aCommentLater.push(parseInt($(item).attr('id').replace('comment_id_', '')));
		});

		this.setCountLaterComment(this.aCommentLater.length);
		this.setCountDirectComment(this.aCommentDirect.length);

		if (curDirect)
		{
			this.iCurrentDirect = $.inArray(curDirect, this.aCommentDirect);
			if (this.iCurrentDirect == -1)
			{
				this.iCurrentDirect = $.inArray(nextDirect, this.aCommentDirect);
				if (this.iCurrentDirect == -1)
					this.iCurrentDirect = -1;
				else
					this.iCurrentDirect--;
			}
		}
		this.updatePanel(sContainerType, iContainerId);
	}

	this.setupComments = function()
	{
		var thisObj = this;

		if (!this.options.bTemplateAction)
		{
			$('.' + this.options.classes.comment).each(function(index, item)
			{
				var id = $(item);
				if (id.length)
				{
					id = id.attr('id');
					if (typeof (id) != 'undefined')
					{
						thisObj.addActionAnswerLater(parseInt(id.replace('comment_id_', '')));
					}
				}
			});
		}

		if (typeof (aAnswerDirectComments) != 'undefined')
		{
			$.each(aAnswerDirectComments, function(index, item)
			{
				thisObj.makeAnswerDirect(item);
			});
		}

		if (typeof (aAnswerLaterComments) != 'undefined')
		{
			$.each(aAnswerLaterComments, function(index, item)
			{
				thisObj.makeAnswerLater(item);
			});
		}

		var updateDiv = $('#update');
		if (updateDiv.length)
		{
			var ssel='a';
			
			if (!iCWLS10)
				ssel='div';
			
			var nc = updateDiv.find(ssel+'#' + this.options.ids.new_comments_counter);
			if (nc.length)
			{
				var ndiv = $('<'+ssel+'>',
				{
					'id' : this.options.ids.later_comments_counter,
					'class' : this.options.classes.later_comments_counter,
					'style' : 'display:none',
					'onclick' : 'ls.cw.core.goToNextLaterAnswer();'
				});
				nc.after(ndiv);
				var ndiv = $('<'+ssel+'>',
				{
					'id' : this.options.ids.direct_comments_counter,
					'class' : this.options.classes.direct_comments_counter,
					'style' : 'display:none',
					'onclick' : 'ls.cw.core.goToNextDirectAnswer();'
				});
				nc.after(ndiv);
			}
		}

		this.afterLoad = function(idTarget, typeTarget, selfIdComment, bNotFlushNew, result)
		{
			$.each(result.aComments, function(index, item)
			{
				thisObj.addActionAnswerLater(item['id']);

				if (item['isAnswerDirect'])
					thisObj.makeAnswerDirect(item['id']);
				if (item['isAnswerLater'])
					thisObj.makeAnswerLater(item['id']);
			});

			thisObj.refreshCommentWatcher();
		}

		this.afterAdd = function(formObj, targetId, targetType, result)
		{
			cmt = thisObj.getComment(result.sParentId);
			if (cmt.length)
			{
				cmt.removeClass(thisObj.options.classes.answer_direct + ' ' + thisObj.options.classes.answer_later);
				thisObj.removeAction(result.sParentId, thisObj.options.classes.action_wont_reply);
				if (cmt.hasClass(ls.comments.options.classes.comment_new))
					thisObj.removeNewComment(result.sParentId);
			}
		}

		this.afterToggle = function(obj, commentId, result)
		{
			cmt = thisObj.getComment(commentId);
			if (cmt.length)
			{
				cmt.removeClass(thisObj.options.classes.answer_direct);
				cmt.removeClass(thisObj.options.classes.answer_later);

				if (!result.bState)
				{
					if (!result.isAnswerDirect)
					{
						cmt.removeClass(thisObj.options.classes.answer_direct);
						thisObj.removeAction(commentId, thisObj.options.classes.action_wont_reply);
					}
					else
					{
						cmt.addClass(thisObj.options.classes.answer_direct);
						thisObj.makeAnswerDirect(commentId);
					}

					if (!result.isAnswerLater)
					{
						cmt.removeClass(thisObj.options.classes.answer_later);
						thisObj.removeAction(commentId, thisObj.options.classes.action_reply_later);
					}
					else
					{
						cmt.addClass(thisObj.options.classes.answer_later);
						thisObj.makeAnswerLater(commentId);
					}
					thisObj.addActionAnswerLater(commentId);
				}
				else
				{
					thisObj.removeAction(commentId, thisObj.options.classes.action_wont_reply);

					thisObj.removeAction(commentId, thisObj.options.classes.action_reply_later);
				}
			}
			thisObj.refreshCommentWatcher();
		}

		ls.hook.add('ls_comments_load_after', [ ls.cw.core, 'afterLoad' ]);
		ls.hook.add('ls_comments_toggle_after', [ ls.cw.core, 'afterToggle' ]);
		ls.hook.add('ls_comments_add_after', [ ls.cw.core, 'afterAdd' ]);
	}

	this.init = function()
	{
		if (typeof (iUserCurrent) != 'undefined')
		{
			if (sContainerType != '')
				this.setupComments();
			this.iCurrentDirect = -1;
			this.iCurrentLater = -1;
			this.aCommentDirect = [];
			this.aCommentLater = [];

			this.refreshCommentWatcher();

			if (this.options.autoupdate_period != 0)
			{
				window.setInterval((function()
				{
					ls.cw.core.updatePanel(sContainerType, iContainerId, true);
				}), this.options.autoupdate_period * 1000);
			}
		}
	}

	return this;
}).call(ls.cw.core ||
{}, jQuery);

jQuery(document).ready(function()
{
	ls.cw.core.init();
});