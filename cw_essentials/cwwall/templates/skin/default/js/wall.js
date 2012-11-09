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
 * Обработка комментариев стены
 */
ls.cw.wall = (function($)
{
	this.options =
	{
		bTemplateAction : true,
		autoupdate_period : 10,
		no_wont_reply : 0,
		classes :
		{
			direct : 'cw-wall-direct',
			indirect : 'cw-wall-indirect',
		}
	}

	this.getComment = function(comment_id)
	{
		return $('#wall-item-' + comment_id);
	}

	this.getCommentReply = function(comment_id)
	{
		return $('#wall-reply-item-' + comment_id);
	}

	this.makeDirect = function(comment_id)
	{
		var thisObj = this;
		var comm = this.getComment(comment_id);
		if (comm.length)
		{
			comm.addClass(this.options.classes.direct);
		}
	}

	this.makeIndirect = function(comment_id)
	{
		var thisObj = this;
		var comm = this.getCommentReply(comment_id);
		if (comm.length)
		{
			comm.addClass(this.options.classes.indirect);
		}
	}


	this.setupWall = function()
	{
		var thisObj = this;

		if (typeof (aDirectWall) != 'undefined')
		{
			$.each(aDirectWall, function(index, item)
			{
				thisObj.makeDirect(item);
			});
		}

		if (typeof (aIndirectWall) != 'undefined')
		{
			$.each(aIndirectWall, function(index, item)
			{
				thisObj.makeIndirect(item);
			});
		}

/*		this.afterLoad = function(idTarget, typeTarget, selfIdComment, bNotFlushNew, result)
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

		ls.hook.add('ls_wall_loadreplynext_after', [ ls.cw.wall, 'afterLoad' ]);
		ls.hook.add('ls_comments_toggle_after', [ ls.cw.wall, 'afterToggle' ]);
		ls.hook.add('ls_comments_add_after', [ ls.cw.wall, 'afterAdd' ]);*/
	}

	this.init = function()
	{
		if (typeof (iUserCurrent) != 'undefined')
		{
			if (sContainerType == 'lswall')
				this.setupWall();
		}
	}

	return this;
}).call(ls.cw.wall ||
{}, jQuery);

jQuery(document).ready(function()
{
	ls.cw.wall.init();
});