				if (typeof(ls.hook)!='undefined') ls.hook.run('ls_comments_add_after',[formObj, targetId, targetType, result]);

				if (typeof(ls.hook)!='undefined') ls.hook.run('ls_comments_load_after',[idTarget, typeTarget, selfIdComment, bNotFlushNew, result]);

				if (typeof(ls.hook)!='undefined') ls.hook.run('ls_comments_toggle_after',[obj,commentId,result]);
