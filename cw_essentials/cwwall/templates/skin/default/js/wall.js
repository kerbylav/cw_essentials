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